
echo "Enter server name:"
read servername
echo "Enter username:"
read username
echo "Enter your database's password:"
read password
echo "Enter DATABASE name:"
read db

touch .env
echo -e "servername=$servername\nusername=$username\npassword=$password\ndb=$db" > .env

php setup.php