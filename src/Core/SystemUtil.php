<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/3/16
 * Time: 11:02
 */
declare(strict_types=1);

namespace PonyCool\Core;

use Carbon\Carbon;
use Exception;
use PonyCool\Core\Util\System;

class SystemUtil
{
    /**
     * 获取系统信息
     * hostname 主机名称
     * system 系统类型
     * os 发型版本
     * kernel 内核版本
     * version PHP版本
     * phpPath PHP路径
     * runMode PHP运行模式
     * user 当前进程用户名
     * bootTime 启动时间
     * uptime 运行时间
     * cpu CPU 核数
     * cpuUsage CPU占用率
     * cpuUsage CPU占用率
     * memoryUsage 内存占用
     * disk 磁盘总容量
     * diskFree 磁盘可用容量
     * oadAverage 平均负载
     * @return array
     */
    public static function systemInfo(): array
    {
        return [
            'hostname' => gethostname(),
            'system' => php_uname('m'),
            'os' => php_uname('s'),
            'kernel' => php_uname('r'),
            'version' => PHP_VERSION,
            'phpPath' => DEFAULT_INCLUDE_PATH,
            'runMode' => php_sapi_name(),
            'user' => get_current_user(),
            'bootTime' => self::bootTime(),
            'uptime' => self::uptime(),
            'cpu' => self::cpu(),
            'cpuUsage' => self::cpuUsage(),
            'memory' => self::memory(),
            'memoryUsage' => self::memoryUsage(),
            'disk' => self::disk(),
            'diskFree' => self::diskFree(),
            'oadAverage' => sys_getloadavg()
        ];
    }

    /**
     * 启动时间
     * @return string
     */
    public static function bootTime(): string
    {
        if (System::inDocker()) {
            $bootTime = '';
            $statContent = file_get_contents('/proc/stat');
            if ($statContent !== false) {
                $lines = explode("\n", $statContent);
                foreach ($lines as $line) {
                    if (str_starts_with($line, 'btime ')) {
                        // 提取启动时间戳
                        $fields = explode(' ', $line);
                        $bootTimestamp = (int)$fields[1];
                        // 将时间戳转换为可读的日期时间格式
                        $bootTime = date('Y-m-d H:i:s', $bootTimestamp);
                        break;
                    }
                }
            }
            return $bootTime;
        }
        $output = shell_exec('who -b');
        if (!is_string($output)) {
            return '';
        }
        $bootTime = explode('boot', $output)[1] ?? null;
        $bootTime = Carbon::createFromTimeString($bootTime);
        return $bootTime->toDateTimeString();
    }

    /**
     * 运行时间
     * @return string
     */
    public static function uptime(): string
    {
        try {
            $bootTime = Carbon::createFromTimeString(self::bootTime());
        } catch (Exception) {
            return '';
        }
        $diff = Carbon::now()->diff($bootTime);
        return sprintf('%d天%d小时%d分钟%d秒', $diff->days, $diff->h, $diff->m, $diff->s);
    }

    /**
     * CPU 核数
     * @return int
     */
    public static function cpu(): int
    {
        $output = shell_exec('sysctl -n hw.ncpu');
        return intval($output);
    }

    /**
     * CPU占用
     * 假设所有 CPU 核心的负载都是均匀的
     * 这里我们只是简单地将负载平均值作为 CPU 占用率
     * 这是一个非常粗略的估计，并不完全准确
     * @return string
     */
    public static function cpuUsage(): string
    {
        // Linux
        if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
            // 获取第一行CPU总体使用信息
            $cpuInfo = file_get_contents('/proc/stat');
            $cpuData = explode(' ', preg_split("/\n/", $cpuInfo)[0]);

            // 计算非idle时间
            $nonIdle = array_sum(array_slice($cpuData, 1, 7));

            // 获取总的CPU时间
            $totalCpuTime = array_sum($cpuData);

            // 上次记录的非idle时间和总时间
            static $prevNonIdle = 0, $prevTotal = 0;

            // 首次运行时初始化历史数据
            if ($prevNonIdle == 0 && $prevTotal == 0) {
                list($prevNonIdle, $prevTotal) = [$nonIdle, $totalCpuTime];
                return '0';
            }

            // 计算CPU使用率
            $deltaTotal = $totalCpuTime - $prevTotal;
            $deltaNonIdle = $nonIdle - $prevNonIdle;
            $cpuUsage = ($deltaTotal - $deltaNonIdle) / $deltaTotal * 100;

            // 更新历史数据
            list($prevNonIdle, $prevTotal) = [$nonIdle, $totalCpuTime];

            return (string)round($cpuUsage, 2);
        } elseif (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN') {
            $loadAvg = sys_getloadavg();
            return (string)$loadAvg[0];
        } else {
            return "";
        }
    }

    /**
     * 内存
     * @return string
     */
    public static function memory(): string
    {
        // 在 Linux 上使用 free 命令获取内存信息
        // 在 macOS 上使用 sysctl 命令获取内存信息
        $os = strtolower(PHP_OS);
        if (str_contains($os, 'linux')) {
            // Linux
            $command = 'free -m | grep Mem: | awk \'{print $2}\'';
            $output = shell_exec($command);
        } elseif (str_contains($os, 'darwin')) {
            // macOS
            $command = 'sysctl hw.memsize | awk \'{print $2 / (1024 * 1024)}\'';
            $output = shell_exec($command);
        } else {
            // 其他系统或未知系统
            return '';
        }

        // 尝试将输出转换为整数
        $memory = (int)$output;

        // 如果转换成功，返回内存大小；否则返回 false
        return $memory ? $memory . ' MB' : '';
    }

    /**
     * 内存占用
     * @return string
     */
    public static function memoryUsage(): string
    {
        return shell_exec('ps -eo pid,rss | awk \'{total += $2} END {print total/1024,"MB"}\'');
    }

    /**
     * 磁盘总容量
     * @return string
     */
    public static function disk(): string
    {
        $total = disk_total_space('.');
        return floor($total / (1024 * 1024 * 1024)) . " GB";
    }

    /**
     * 磁盘可用容量
     * @return string
     */
    public static function diskFree(): string
    {
        $total = disk_free_space('.');
        return floor($total / (1024 * 1024 * 1024)) . " GB";
    }
}