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
