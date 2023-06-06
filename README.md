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
        "repayments": [
            {
                "due_at": "2022-02-14",
                "currency": "USD",
                "amount": 3333.33,
                "state": "PENDING"
            },
            {
                "due_at": "2022-02-21",
                "currency": "USD",
                "amount": 3333.33,
                "state": "PENDING"
            },
            {
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

## Submit Repayment
