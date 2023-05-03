# Code Challenge

### Notes From Author

From my view, there still lot of room for improvement. Because the time is limit, I can't finish all. So I would like
to bullet some of them below.

### First let go with what I've done so far:

- Implement versioning for easily extend/maintain the APIs. You guys might want to look into the route list/
- [](https://)At now, the business logics were binding to the framework itself. It never a good practice. However, this will take
  more time for implement an architect such as Clean Architect, to be able to completely separated.
- There a lot of Features tests and Unit tests need to accomplished, just finish some cases.
- Using [Lumen](https://lumen.laravel.com/docs/10.x) as the framework. It very light weight to compare against the fatty
  Laravel.
- MySQL as the database. However, If i have chance, I will use MongoDB instead.
- Some lib I used in the codebase:
  - https://github.com/tymondesigns/jwt-auth
  - https://github.com/flipboxstudio/lumen-generator
  - https://github.com/briannesbitt/carbon

### Then the Todo things:

- There still more features to add to be able to deliver to customer such as:
  - Need to validate the time borrower add repayment against the scheduled payments
  - Borrower need to be notified in some cases:
    - Admin update the loan status from PENDING to APPROVED
    - They completed the scheduled payments, and the loan was changed from APPROVED to PAID

### How to set up on your local environment

1. Make sure docker desktop installed
2. clone the repository
3. Navigate to folder via command prompt or some terminal
4. Run `docker compose up --build -d`
5. Check my code while waiting for the building process
6. Please make sure run those:
   1. `docker compose exec api php artisan migrate:fresh`
   2. `docker compose exec api php artisan db:seed`
7. For running test suite please use `docker compose exec api php vendor/phpunit/phpunit/phpunit tests `
8. Postman collection can be found [at this link](https://drive.google.com/file/d/1YR3XwlHekvH46OIj2j1lNkl2yndtAi5u/view?usp=share_link)
9. List test users can be found at UserSeeder class

_If you have any concern or question, I'm very happy to answer those, touch me at minhbq4819@gmail.com_
