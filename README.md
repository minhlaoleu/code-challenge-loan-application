# Loan Application
## Todo:
- System Design
  - List Endpoints:
    - /api/security/token
      - GET: username + password
      - DELETE: logout
    - /api/v1/loan
      - POST: submit loan request with two required fields amount and term
      - GET: Get list amortization schedules with Status
        - permission
          - customer: can view only loan belong to them
          - admin: can change status of the loan
    - /api/v1/loans/{id}/payments
      - POST: add new payment, the loan amount must be equal or greate current amount
      - GET: Get list amortization schedules with Status
        - Customer add a repayment with amount greater or equal to the scheduled repayment
        - The scheduled repayment change the status to PAID
        - If all the scheduled repayments connected to a loan are PAID automatically also the loan become PAID
- Coding
  - Lumen
  - MySQL
- Documentation
  - openAPI
  - Postman collection
  - Brief document
  - Dockerfile
- Using Package
  - https://github.com/tymondesigns/jwt-auth
  - https://github.com/flipboxstudio/lumen-generator