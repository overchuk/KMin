#!/bin/sh

tt()
{
while read name text
do
	echo -n "\t$text\t\t\t"
	../_script/test.sh $name
done	
}

rd()
{

while read name text 
do
	echo $text
	cd $name
	echo -n "" > errors.txt
	cat list.txt | tt
	E=`cat errors.txt`
	if [ ! -z "$E" ]
	then
		#echo "----------------------"
		#echo "[0;31;40mERRORS:[0;37;40m $E"
		#echo
		echo "$name : $E" >>  ../errors.txt
	fi
	echo
	cd - > /dev/null
done
	
}

echo -n "" > errors.txt
cat list.txt | rd
E=`cat errors.txt`

echo 
echo '============================='
if [ ! -z "$E" ]
then
	echo "[0;31;40mERRORS:[0;37;40m"
	echo "$E"
else
	echo "[0;32;40mALL TEST IS OK:[0;37;40m"
fi
echo



