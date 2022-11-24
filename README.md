This application is a system of RESTful API endpoints to perform CRUD operations on an invoice and to allow a user to check in into a fitness club.

## Running the application
1. Clone the repository in your local system: `git clone git@github.com:recruitmentvg/nadasanraul.git`.
2. Navigate to the directory where the repository was cloned: `cd nadasanraul`.
3. Create the `.env` file: `cp .env.example .env`.
4. Open the `.env` file in your favorite code editor and populate the `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` with your chosen values.
5. Build the app image: `docker-compose build app`.
6. Instantiate the docker container: `docker-compose up -d`.
7. Install the composer packages: `docker-compose exec app composer install`.
8. Generate the application key: `docker-compose exec app php artisan key:generate`.
9. Migrate the database: `docker-compose exec app php artisan migrate`.
10. (Optional) If you want to have dummy data, run `docker-compose exec app php artisan db:seed`.
11. Now you can access the application at `http://localhost:8000`.


## Running the tests
Once the application is running, you can run the test suite by running `docker-compose exec app ./vendor/bin/phpunit`.

## Endpoints

### GET `/api/invoices`
This endpoint handles fetching all the invoices, together with the lines for each invoice and the user it belongs to.

#### Response
Code: `200`
```json
[
    {
        "id": 1,
        "status": "Outstanding",
        "description": "Cum molestiae sequi in laboriosam nisi aut earum.",
        "amount": 10000,
        "date": "2022-11-24 20:57:21",
        "lines": [
            {
                "id": 1,
                "invoice_id": 2,
                "amount": 5000,
                "description": "Animi necessitatibus sed ullam cupiditate ipsa deserunt sunt."
            },
            {
                "id": 2,
                "invoice_id": 2,
                "amount": 5000,
                "description": "Et excepturi earum illo."
            }
        ],
        "user": {
            "id": 1,
            "name": "Ms. Virgie Hartmann",
            "email": "emmett30@example.net"
        }
    }
]
```
<br/>

### POST `/api/invoices`
Handles the creation of a new invoice. It is created without any lines, with an amount of 0 and with the status as `Outstanding`

#### Body
| Name           | Required | Type    | Description                                                                         |
|----------------|----------|---------|-------------------------------------------------------------------------------------|
| `user_id`      | required | integer | The ID of the user the invoice will belong to                                       |
| `description`  | required | string  | A short phrase to explain what the invoice is about<br/> Max length: 255 characters |

#### Response
Code: `200`
```json
{
    "id": 13,
    "status": "Outstanding",
    "description": "This is the invoice for 11-2022",
    "amount": 0,
    "date": "2022-11-24 21:17:12",
    "lines": [],
    "user": {
        "id": 6,
        "name": "Ms. Virgie Hartmann",
        "email": "emmett30@example.net"
    }
}
```
<br/>

### GET `/api/invoices/{id}`
Fetches a single invoice based on its id, together with the associated invoice lines and the user

#### Parameters
| Name  | Required | Type    | Description                         |
|-------|----------|---------|-------------------------------------|
| `id`  | required | integer | The id of the invoice to be fetched |

#### Response
Code: `200`
```json
{
    "id": 2,
    "status": "Outstanding",
    "description": "Cum molestiae sequi in laboriosam nisi aut earum.",
    "amount": 10000,
    "date": "2022-11-24 20:57:21",
    "lines": [
        {
            "id": 12,
            "invoice_id": 2,
            "amount": 5000,
            "description": "Animi necessitatibus sed ullam cupiditate ipsa deserunt sunt."
        },
        {
            "id": 13,
            "invoice_id": 2,
            "amount": 5000,
            "description": "Et excepturi earum illo."
        }
    ],
    "user": {
        "id": 2,
        "name": "Dr. Hollie Pacocha",
        "email": "verla.oreilly@example.com"
    }
}
```
<br/>

### PATCH `/api/invoices/{id}`
Updating a single invoice based on the id. The response returns the updated invoice with the invoice lines and user.

#### Parameters
| Name  | Required | Type    | Description                         |
|-------|----------|---------|-------------------------------------|
| `id`  | required | integer | The id of the invoice to be updated |

#### Body
| Name         | Required | Type   | Description                                                                                        |
|--------------|----------|--------|----------------------------------------------------------------------------------------------------|
| `description` | optional | string | A short phrase to explain what the invoice is about<br/> Max length: 255 characters                |
| `status`      | optional | string | The new status to be set on the invoice<br/>Eligible values are: `Outstanding`, `Paid` and `Void`  |

#### Response
Code: `200`
```json
{
    "id": 2,
    "status": "Void",
    "description": "Updated description",
    "amount": 10000,
    "date": "2022-11-24 22:34:10",
    "lines": [
        {
            "id": 12,
            "invoice_id": 2,
            "amount": 5000,
            "description": "Animi necessitatibus sed ullam cupiditate ipsa deserunt sunt."
        },
        {
            "id": 13,
            "invoice_id": 2,
            "amount": 5000,
            "description": "Et excepturi earum illo."
        }
    ],
    "user": {
        "id": 2,
        "name": "Dr. Hollie Pacocha",
        "email": "verla.oreilly@example.com"
    }
}
```
<br/>

### DELETE `/api/invoices/{id}`
Deleting a single invoice. Deleting is only allowed if the invoice has no lines associated with it.

#### Parameters
| Name  | Required | Type    | Description                         |
|-------|----------|---------|-------------------------------------|
| `id`  | required | integer | The id of the invoice to be deleted |

#### Response
Code: `204`
<br/>
<br/>
### POST `/api/invoices/{id}/lines`
Adds an invoice lines to an invoice. The amount of the invoice will be incremented by the amount of the line. Returns the line being added.

#### Parameters
| Name  | Required | Type    | Description                                     |
|-------|----------|---------|-------------------------------------------------|
| `id`  | required | integer | The id of the invoice the line will be added to |

#### Body
| Name           | Required | Type    | Description                                                                           |
|----------------|----------|---------|---------------------------------------------------------------------------------------|
| `amount`       | required | integer | The amount associated with the line. Must be positive.                                |
| `description`  | required | string  | Short phrase to explain what the invoice line is about<br/>Max length: 255 characters |

#### Response
Code: `200`
```json
{
    "amount": 1000,
    "description": "Check in on 24-11-2022",
    "invoice_id": 12,
    "id": 112
}
```
<br/>

### POST `/api/users/{id}/checkin`
Checks in the user into a fitness club if they have a valid membership. An invoice line is added every time the user successfully checks in, and 1 credit is subtracted from their membership.

#### Parameters
| Name  | Required | Type    | Description                    |
|-------|----------|---------|--------------------------------|
| `id`  | required | integer | The id of the user checking in |

### Response
Code: `204`

## Improvements I would add
* Adding caching for the user and invoices.
* Map the request body to DTO objects before they're injected into the controller methods.
* Paginate the response for the endpoint that gets all the invoices.
* Implement [API resources](https://laravel.com/docs/9.x/eloquent-resources) to serve as response objects instead of returning the serialised eloquent models.
* Dispatching a queue job to add the invoice line when the user checks in instead of doing it on the main thread.
* Create a `CrudServiceInterface`, and `CrudInvoiceService` that would extend said interface and would encapsulate the logic to perform CRUD operations on the invoices.

