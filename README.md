# Demo Load Repayment API

# Launch

Prepare the `.env` file (if not yet)

```bash
cp .env.example .env
```

Install the dependencies

```bash
docker run --rm -v $PWD:/app composer install
```

Run the app using sail

```bash
./sail up

```

Run migration

```bash
./sail artisan db:migrate
```

# API(s)

Base API URL: `https://localhost/api`

All API Endpoint except register and login require Authorization header.

```
Authorization: Bearer <access token from register/login api>
```

example:

```
Authorization: Bearer 7|amWM2mZXDwEmfYTiUb5XUXuIgzFtDhJGDvxDWnUp
```

## Register

POST - /auth/register
Request (Admin Role):

```json
{
    "name": "Admin Hugh Baumbach",
    "email": "admin@fakemail.com",
    "password": "password",
    "password_confirmation": "password",
    "role": "admin"
}
```

Request (Customer Role):

```json
{
    "name": "Customer Percy Swaniawski",
    "email": "customer@fakemail.com",
    "password": "password",
    "password_confirmation": "password",
    "role": "customer"
}
```

Response:

```json
{
    "message": "Registration successful",
    "user": {
        "name": "Customer Percy Swaniawski",
        "email": "customer@fakemail.com",
        "role": "customer",
        "updated_at": "2023-06-10T20:16:11.000000Z",
        "created_at": "2023-06-10T20:16:11.000000Z",
        "id": 1
    },
    "access_token": "1|uzt4irBhqvZb1jYMG1LHPOvCHtnQDyZNV3DJL0iI"
}
```

## Submit Loan

POST - /loans

Request:

```json
{
    "loan": {
        "currency": "USD",
        "amount": 10000,
        "term": 3,
        "submitted_at": "2022-02-07"
    }
}
```

Response:

```json
{
    "loan": {
        "id": "<generated id>",
        "currency": "USD",
        "amount": 10000,
        "term": 3,
        "submitted_at": "2022-02-07",
        "state": "PENDING",
        "scheduled_repayments": [
            {
                "id": "<generated id>",
                "due_at": "2022-02-14",
                "currency": "USD",
                "amount": 3333.33,
                "state": "PENDING"
            },
            {
                "id": "<generated id>",
                "due_at": "2022-02-21",
                "currency": "USD",
                "amount": 3333.33,
                "state": "PENDING"
            },
            {
                "id": "<generated id>",
                "due_at": "2022-02-21",
                "currency": "USD",
                "amount": 3333.34,
                "state": "PENDING"
            }
        ]
    }
}
```

## Approve The Load - Admin Only

POST - /loans/{loanId}/approve

Request:

```json

```

Response:

```json
{
    "loan": {
        "id": "<generated id>",
        "currency": "USD",
        "amount": 10000,
        "term": 3,
        "submitted_at": "2022-02-07",
        "state": "APPROVED"
    }
}
```

## View Loan(s) - Customer Owned Loan

GET - /loans

Response:

```json
{
    "loans": [
        {
            "id": "<generated id>",
            "currency": "USD",
            "amount": 10000,
            "term": 3,
            "submitted_at": "2022-02-07",
            "state": "PENDING"
        }
    ]
}
```

## Submit Repayment

POST - /loans/{loanId}/repayments

Request:

```json
{
    "repayment": {
        "currency": "USD",
        "amount": 10000,
        "scheduled_repayment_id": "<scheduled_repayment_id>"
    }
}
```

```json
{
    "repayment": {
        "id": "<generated_id>",
        "loan": {
            "id": "{loanId}",
            "status": "APPROVED"
        },
        "scheduled_repayment": {
            "id": "<scheduled_repayment_id>",
            "status": "PAID"
        },
        "currency": "USD",
        "amount": 10000
    }
}
```
