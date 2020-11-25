#!/bin/sh
/sbin/ifconfig eth0 down
/sbin/ifconfig eth0 10.10.10.10 netmask 255.255.255.0 up
echo   "# Configure Loopback" > /etc/network/interfaces
echo "auto lo" >> /etc/network/interfaces
echo "iface lo inet loopback" >> /etc/network/interfaces
echo "# Configure eth0" >> /etc/network/interfaces
echo "auto eth0" >> /etc/network/interfaces
echo "iface eth0 inet static" >> /etc/network/interfaces
echo "address 10.10.10.10" >> /etc/network/interfaces
echo "network 10.10.10.0" >> /etc/network/interfaces
echo "netmask 255.255.255.0" >> /etc/network/interfaces
echo "broadcast 10.10.10.255" >> /etc/network/interfaces
echo "gateway 10.10.10.1" >> /etc/network/interfaces

echo "search domain.com" > /etc/resolv.conf
echo "nameserver 8.8.8.8" >> /etc/resolv.conf
echo "nameserver 8.8.4.4" >> /etc/resolv.conf
ip route add default via 10.10.10.1
grep address /etc/network/interfaces | cut -f 2 -d ' ' > /tmp/sdip
/sbin/reboot > /dev/null 2>&1 &