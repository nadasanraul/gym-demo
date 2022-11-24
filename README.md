This application is a system of RESTful API endpoints to perform CRUD operations on an invoice and to allow a user to check in into a fitness club.

### Endpoints

#### Invoices
* `GET     /api/invoices - Returns all the invoices`
* `POST    /api/invoices - Creates a new invoice`
* `GET    /api/invoices/{id} - Returns a single invoice`
* `PATCH  /api/invoices/{id} - Updates an invoice`
* `DELETE /api/invoices/{id} - Deletes an invoice`
* `POST   /api/invoices/{id}/lines - Addes an invoice line to an invoice`

#### Users
* `POST /api/users/checkin - Checks in a user in a fitnes club`
