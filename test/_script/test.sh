if [ -z "$1" ]
then
	echo "Need test name parameter"
	exit
fi

cd $1
php $1.php > $1.res
diff $1.ori $1.res > $1.err
if [ $? -eq 0 ]
then
	echo "[0;32;40mOK[0;37;40m"
else
	echo "[0;31;40mFAIL[0;37;40m"
	echo " $1 " >> ../errors.txt
fi
