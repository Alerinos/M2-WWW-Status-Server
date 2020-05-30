#!/bin/bash
echo '{'
echo '"channels" : ['

for i in {1..4}
do
   for j in {3..4}
	do
		pid=$(ps -ef | grep c$i$j | awk '{print $1}')
		[ -z "$pid" ] && pid=0
		time=$(ps -eo etimes,args | grep c$i$j | grep -v grep | awk '{print $1}')
		[ -z "$time" ] && time=0
		online=$(netstat -an | grep 30$i$j''0 | wc -l)
		[ -z "$online" ] && online=0

		echo '
		{
		 "name":"C'$i'-'$j'",
		 "pid":'$pid',
		 "time":'$time',
		 "online":'$online'
		},'
	done
done
  
pid=$(ps -ef | grep c991 | awk '{print $1}')
[ -z "$pid" ] && pid=0
time=$(ps -eo etimes,args | grep c991 | grep -v grep | awk '{print $1}')
[ -z "$time" ] && time=0
online=$(netstat -an | grep 30910 | wc -l)
[ -z "$online" ] && online=0

echo '
{
 "name":"C99-1",
 "pid":'$pid',
 "time":'$time',
 "online":'$online'
},'

pid=$(ps -ef | grep c992 | awk '{print $1}')
[ -z "$pid" ] && pid=0
time=$(ps -eo etimes,args | grep c992 | grep -v grep | awk '{print $1}')
[ -z "$time" ] && time=0
online=$(netstat -an | grep 30920 | wc -l)
[ -z "$online" ] && online=0

echo '
{
 "name":"C99-2",
 "pid":'$pid',
 "time":'$time',
 "online":'$online'
},'

pid=$(ps -ef | grep c993 | awk '{print $1}')
[ -z "$pid" ] && pid=0
time=$(ps -eo etimes,args | grep c993 | grep -v grep | awk '{print $1}')
[ -z "$time" ] && time=0
online=$(netstat -an | grep 30930 | wc -l)
[ -z "$online" ] && online=0

echo '
{
 "name":"C99-3",
 "pid":'$pid',
 "time":'$time',
 "online":'$online'
}'


echo '],'

dbpid=$(ps -ef | grep mt_db | awk '{print $1}')
[ -z "$dbpid" ] && dbpid=0
dbtime=$(ps -eo etimes,args | grep mt_db | grep -v grep | awk '{print $1}')
[ -z "$dbtime" ] && dbtime=0

echo '
"db" : {
 "pid":'$dbpid',
 "time":'$dbtime'
},'
  
authpid=$(ps -ef | grep mt_auth | awk '{print $1}')
[ -z "$authpid" ] && authpid=0
authtime=$(ps -eo etimes,args | grep mt_auth | grep -v grep | awk '{print $1}')
[ -z "$authtime" ] && authtime=0

echo '
"auth" : {
 "pid":'$authpid',
 "time":'$authtime'
}'

echo '}'
