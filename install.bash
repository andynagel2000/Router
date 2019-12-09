yum update
yum install nginx
firewall-cmd --permanent --add-port=80/tcp
firewall-cmd --reload
systemctl start nginx 
systemctl enable nginx
yum install php
yum install php-fpm
systemctl start php-fpm
systemctl enable php-fpm
