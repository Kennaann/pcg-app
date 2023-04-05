alias pcg-sf='docker exec -it pcg_php php bin/console'
alias pcg-mig='docker exec -it pcg_php php bin/console doctrine:migrations:migrate'
alias pcg-fixtures='docker exec -it pcg_php php bin/console doctrine:fixtures:load'