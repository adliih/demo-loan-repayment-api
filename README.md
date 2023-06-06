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

## Submit Load

## Approve The Load - Admin Only

## View Loan(s) - Customer Owned Loan

## Submit Repayment
