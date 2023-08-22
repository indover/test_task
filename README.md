Test Task:
Utilizing the Yii2 framework, implement a logging application consisting of a set of three console commands.

Console command that monitors the Nginx access.log file and, upon finding new data, stores it in Clickhouse.
Console command that accepts mandatory parameters 'startDate' and 'finishDate' and outputs all saved records from the Clickhouse DB within the specified time interval.
Console command that accepts mandatory parameters 'startDate' and 'finishDate' and outputs the count of saved records in the Clickhouse DB within the specified time interval.
Requirements:

Language: PHP, Framework: Yii2.
Possible utilization of any additional libraries.
Infrastructure described in docker-compose.yml (Nginx, Clickhouse, php-fpm, etc.).
Table for log storage in Clickhouse must be created through migration. The table structure is arbitrary.
Application configuration should be managed through the .env file.
The task should contain a Readme file with instructions for setup and execution.
In case of questions, the decision-making process is left to the candidate (in such cases, the Readme file should include a list of questions the candidate encountered and the approach taken to solve them).
------------------------------------------------------------------------------------------------------------------------
log file = .env -> log/access.log

1. Copy project to folder.
2. Update .env file.
3. Run docker command - docker compose build || docker compose up -d + docker compose exec php bash -> composer install
4. Run migration - php yii clickhouse-migrate.
5. Run console command lo listen log file - php yii read-log/list
6. Run:
   * php yii click-house/raws startDate finishDate -> to get data between input dates
   * php yii click-house/add-test -> add test data
   * php yii click-house/get-count -> to get count of total data
   * php yii click-house/count startDate finishDate -> to get count of total data between input dates