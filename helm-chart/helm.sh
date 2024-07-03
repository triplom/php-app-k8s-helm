helm install phpfpm-app \
  --set mariadb.mariadbRootPassword=mini \
  --set mariadb.mariadbUser=mini \
  --set mariadb.mariadbPassword=mini \
  --set mariadb.mariadbDatabase=mini \
  .
