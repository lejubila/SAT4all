<?php

namespace App\Tools\LinuxCheatsheet;

class LinuxCheatsheet
{
    /**
     * Comandi raggruppati per categoria.
     * Le descrizioni sono in inglese nel sorgente; le categorie sono
     * tradotte tramite la chiave tools.linux_cheatsheet.cat_{key}.
     *
     * @return array<string, array<int, array{cmd: string, desc: string, example: string}>>
     */
    public static function categories(): array
    {
        return [
            'filesystem' => [
                ['cmd' => 'ls -la',                    'desc' => 'List all files with permissions and sizes',         'example' => 'ls -la /var/log'],
                ['cmd' => 'pwd',                        'desc' => 'Print current working directory',                   'example' => 'pwd'],
                ['cmd' => 'cd /path',                   'desc' => 'Change directory',                                  'example' => 'cd /etc/nginx'],
                ['cmd' => 'mkdir -p dir/sub',           'desc' => 'Create directory tree (with parents)',              'example' => 'mkdir -p /opt/app/config'],
                ['cmd' => 'rm -rf dir',                 'desc' => 'Remove files or directories recursively',           'example' => 'rm -rf /tmp/old-build'],
                ['cmd' => 'cp -r src dst',              'desc' => 'Copy files or directories recursively',             'example' => 'cp -r /etc/nginx /backup/nginx'],
                ['cmd' => 'mv src dst',                 'desc' => 'Move or rename files and directories',              'example' => 'mv app.conf app.conf.bak'],
                ['cmd' => 'find / -name "*.log"',       'desc' => 'Find files by name pattern',                        'example' => 'find /var -name "*.log" -mtime +7'],
                ['cmd' => 'chmod 755 file',             'desc' => 'Change file permissions (octal or symbolic)',       'example' => 'chmod u+x deploy.sh'],
                ['cmd' => 'chown user:group file',      'desc' => 'Change file owner and group',                      'example' => 'chown www-data:www-data /var/www'],
                ['cmd' => 'ln -s target link',          'desc' => 'Create a symbolic link',                           'example' => 'ln -s /etc/nginx/sites-available/app /etc/nginx/sites-enabled/app'],
                ['cmd' => 'du -sh dir',                 'desc' => 'Show disk usage of a directory (human-readable)',  'example' => 'du -sh /var/log/*'],
                ['cmd' => 'stat file',                  'desc' => 'Detailed file metadata (size, inode, timestamps)', 'example' => 'stat /etc/passwd'],
            ],
            'text' => [
                ['cmd' => 'cat file',                   'desc' => 'Display file content',                             'example' => 'cat /etc/hosts'],
                ['cmd' => 'less file',                  'desc' => 'Paginate file content (q to quit)',                'example' => 'less /var/log/syslog'],
                ['cmd' => 'head -n 20 file',            'desc' => 'Show first N lines of a file',                    'example' => 'head -n 50 /var/log/nginx/access.log'],
                ['cmd' => 'tail -f file',               'desc' => 'Follow file changes in real time',                'example' => 'tail -f /var/log/nginx/error.log'],
                ['cmd' => 'grep -r "pattern" .',        'desc' => 'Search text recursively in current directory',    'example' => 'grep -r "ERROR" /var/log/'],
                ['cmd' => 'grep -i -n "pattern" file',  'desc' => 'Case-insensitive search with line numbers',       'example' => 'grep -in "timeout" app.log'],
                ['cmd' => 'sed -i \'s/old/new/g\' file','desc' => 'Replace all occurrences in file (in-place)',      'example' => 'sed -i \'s/localhost/10.0.0.1/g\' config.ini'],
                ['cmd' => 'awk \'{print $1}\' file',    'desc' => 'Print specific column from file',                 'example' => 'awk \'{print $1,$7}\' access.log'],
                ['cmd' => 'cut -d: -f1 /etc/passwd',   'desc' => 'Extract field using delimiter',                   'example' => 'cut -d: -f1,3 /etc/passwd'],
                ['cmd' => 'sort -n file',               'desc' => 'Sort lines (numerically with -n)',                 'example' => 'sort -rn sizes.txt'],
                ['cmd' => 'uniq -c',                    'desc' => 'Count and deduplicate consecutive lines',          'example' => 'sort access.log | uniq -c | sort -rn'],
                ['cmd' => 'wc -l file',                 'desc' => 'Count lines, words or bytes',                     'example' => 'wc -l /etc/passwd'],
                ['cmd' => 'diff file1 file2',           'desc' => 'Compare two files line by line',                  'example' => 'diff nginx.conf nginx.conf.bak'],
            ],
            'processes' => [
                ['cmd' => 'ps aux',                     'desc' => 'List all running processes',                       'example' => 'ps aux | grep nginx'],
                ['cmd' => 'top',                        'desc' => 'Interactive process and resource monitor',         'example' => 'top -u www-data'],
                ['cmd' => 'htop',                       'desc' => 'Improved interactive process viewer',             'example' => 'htop'],
                ['cmd' => 'kill -9 PID',                'desc' => 'Force-kill a process by PID (SIGKILL)',           'example' => 'kill -9 1234'],
                ['cmd' => 'killall name',               'desc' => 'Kill all processes by name',                      'example' => 'killall php-fpm'],
                ['cmd' => 'pgrep -l name',              'desc' => 'Find PIDs by process name',                      'example' => 'pgrep -l nginx'],
                ['cmd' => 'nohup cmd &',                'desc' => 'Run command immune to hangups, in background',    'example' => 'nohup ./long-script.sh &'],
                ['cmd' => 'jobs',                       'desc' => 'List background jobs in current shell',           'example' => 'jobs -l'],
                ['cmd' => 'systemctl status svc',       'desc' => 'Show service status (systemd)',                   'example' => 'systemctl status nginx'],
                ['cmd' => 'systemctl restart svc',      'desc' => 'Restart a systemd service',                      'example' => 'systemctl restart php8.3-fpm'],
                ['cmd' => 'systemctl enable svc',       'desc' => 'Enable service to start at boot',                 'example' => 'systemctl enable docker'],
                ['cmd' => 'journalctl -u svc -f',       'desc' => 'Follow logs for a systemd service',              'example' => 'journalctl -u nginx -f --since "1 hour ago"'],
                ['cmd' => 'nice -n 10 cmd',             'desc' => 'Run command with lower CPU priority',             'example' => 'nice -n 19 tar -czf backup.tgz /data'],
            ],
            'network' => [
                ['cmd' => 'ip addr show',               'desc' => 'Show all network interfaces and IPs',             'example' => 'ip addr show eth0'],
                ['cmd' => 'ip route show',              'desc' => 'Show routing table',                              'example' => 'ip route show'],
                ['cmd' => 'ss -tulnp',                  'desc' => 'Show listening TCP/UDP ports with PIDs',          'example' => 'ss -tulnp | grep :80'],
                ['cmd' => 'netstat -tulnp',             'desc' => 'Legacy: show open ports (net-tools)',             'example' => 'netstat -tulnp'],
                ['cmd' => 'ping -c 4 host',             'desc' => 'Send ICMP echo requests to host',                'example' => 'ping -c 4 8.8.8.8'],
                ['cmd' => 'traceroute host',            'desc' => 'Trace packet route to host',                     'example' => 'traceroute google.com'],
                ['cmd' => 'curl -I url',                'desc' => 'Fetch HTTP headers only',                        'example' => 'curl -I https://example.com'],
                ['cmd' => 'curl -sL url',               'desc' => 'Download URL silently, following redirects',     'example' => 'curl -sL https://api.example.com/status'],
                ['cmd' => 'wget url',                   'desc' => 'Download file from URL',                         'example' => 'wget -O app.tar.gz https://example.com/app.tar.gz'],
                ['cmd' => 'ssh user@host',              'desc' => 'Open SSH session to remote host',                'example' => 'ssh -p 2222 admin@10.0.0.1'],
                ['cmd' => 'scp file user@host:/path',   'desc' => 'Securely copy file to/from remote host',        'example' => 'scp backup.tar.gz user@192.168.1.10:/backups/'],
                ['cmd' => 'rsync -avz src dst',         'desc' => 'Sync files (archive, verbose, compressed)',      'example' => 'rsync -avz /var/www/ user@backup:/var/www/'],
                ['cmd' => 'nmap -sV host',              'desc' => 'Scan host ports and detect service versions',    'example' => 'nmap -sV -p 22,80,443 10.0.0.1'],
                ['cmd' => 'lsof -i :80',                'desc' => 'Show processes using a specific port',           'example' => 'lsof -i :443'],
            ],
            'system' => [
                ['cmd' => 'uname -a',                   'desc' => 'Print kernel name, version and architecture',    'example' => 'uname -a'],
                ['cmd' => 'uptime',                     'desc' => 'System uptime and load averages',                'example' => 'uptime'],
                ['cmd' => 'df -h',                      'desc' => 'Disk space usage (human-readable)',              'example' => 'df -h /var'],
                ['cmd' => 'free -h',                    'desc' => 'RAM and swap memory usage',                      'example' => 'free -h'],
                ['cmd' => 'lscpu',                      'desc' => 'CPU architecture information',                   'example' => 'lscpu'],
                ['cmd' => 'lsblk',                      'desc' => 'List block devices (disks and partitions)',      'example' => 'lsblk -f'],
                ['cmd' => 'dmesg | tail -50',           'desc' => 'Kernel ring buffer messages (last 50)',          'example' => 'dmesg | grep -i error'],
                ['cmd' => 'who',                        'desc' => 'Show currently logged-in users',                 'example' => 'who'],
                ['cmd' => 'last',                       'desc' => 'Show login history',                            'example' => 'last -n 20'],
                ['cmd' => 'env',                        'desc' => 'Show all environment variables',                 'example' => 'env | grep PATH'],
                ['cmd' => 'hostname -I',                'desc' => 'Show all IP addresses of this host',             'example' => 'hostname -I'],
                ['cmd' => 'timedatectl',                'desc' => 'Show and set system time/timezone',              'example' => 'timedatectl set-timezone Europe/Rome'],
            ],
            'users' => [
                ['cmd' => 'whoami',                     'desc' => 'Print current username',                         'example' => 'whoami'],
                ['cmd' => 'id user',                    'desc' => 'Show user and group IDs',                       'example' => 'id www-data'],
                ['cmd' => 'useradd -m -s /bin/bash user','desc' => 'Create new user with home directory',          'example' => 'useradd -m -s /bin/bash deploy'],
                ['cmd' => 'passwd user',                'desc' => 'Set or change user password',                    'example' => 'passwd deploy'],
                ['cmd' => 'usermod -aG group user',     'desc' => 'Add user to supplementary group',               'example' => 'usermod -aG docker deploy'],
                ['cmd' => 'userdel -r user',            'desc' => 'Delete user and home directory',                'example' => 'userdel -r olduser'],
                ['cmd' => 'su - user',                  'desc' => 'Switch to another user (login shell)',           'example' => 'su - postgres'],
                ['cmd' => 'sudo -l',                    'desc' => 'List current user\'s sudo permissions',         'example' => 'sudo -l'],
                ['cmd' => 'visudo',                     'desc' => 'Safely edit /etc/sudoers',                      'example' => 'visudo'],
                ['cmd' => 'groups user',                'desc' => 'Show groups a user belongs to',                 'example' => 'groups deploy'],
            ],
            'archives' => [
                ['cmd' => 'tar -czf out.tgz dir/',      'desc' => 'Create compressed gzip archive',                'example' => 'tar -czf backup-$(date +%F).tgz /var/www/'],
                ['cmd' => 'tar -xzf archive.tgz',       'desc' => 'Extract gzip archive',                         'example' => 'tar -xzf backup.tgz -C /restore/'],
                ['cmd' => 'tar -cjf out.tar.bz2 dir/',  'desc' => 'Create bzip2 compressed archive',              'example' => 'tar -cjf logs.tar.bz2 /var/log/'],
                ['cmd' => 'tar -tf archive.tgz',        'desc' => 'List archive contents without extracting',     'example' => 'tar -tf backup.tgz | head'],
                ['cmd' => 'zip -r archive.zip dir/',    'desc' => 'Create zip archive',                           'example' => 'zip -r site.zip /var/www/html/'],
                ['cmd' => 'unzip archive.zip',          'desc' => 'Extract zip archive',                          'example' => 'unzip site.zip -d /var/www/html/'],
                ['cmd' => 'gzip file',                  'desc' => 'Compress file (replaces original)',             'example' => 'gzip large-dump.sql'],
                ['cmd' => 'gunzip file.gz',             'desc' => 'Decompress gzip file',                         'example' => 'gunzip large-dump.sql.gz'],
            ],
            'disk' => [
                ['cmd' => 'lsblk -f',                   'desc' => 'List block devices with filesystem types',      'example' => 'lsblk -f'],
                ['cmd' => 'blkid',                      'desc' => 'Show block device attributes and UUIDs',        'example' => 'blkid /dev/sdb1'],
                ['cmd' => 'fdisk -l',                   'desc' => 'List disk partitions',                         'example' => 'fdisk -l /dev/sdb'],
                ['cmd' => 'parted -l',                  'desc' => 'List partitions (supports GPT)',                'example' => 'parted /dev/sdb print'],
                ['cmd' => 'mount /dev/sdb1 /mnt',       'desc' => 'Mount a filesystem',                           'example' => 'mount -t ext4 /dev/sdb1 /mnt/data'],
                ['cmd' => 'umount /mnt',                'desc' => 'Unmount a filesystem',                         'example' => 'umount /mnt/data'],
                ['cmd' => 'mkfs.ext4 /dev/sdb1',        'desc' => 'Format partition as ext4',                     'example' => 'mkfs.ext4 -L data /dev/sdb1'],
                ['cmd' => 'fsck /dev/sdb1',             'desc' => 'Check and repair filesystem',                  'example' => 'fsck -y /dev/sdb1'],
            ],
        ];
    }
}
