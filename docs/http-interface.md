# HTTP interface

SiMonika exposes an authenticated browser interface rather than a public API. Mutation endpoints accept form data and return either redirects for server rendered flows or JSON for modal interactions.

## Application inventory

| Method | Path | Purpose |
| --- | --- | --- |
| GET | `/aplikasi` | List and filter applications |
| POST | `/aplikasi` | Create an application and its attribute values |
| GET | `/aplikasi/{id}/detail` | Read application details as JSON |
| GET | `/aplikasi/{id}/edit` | Read edit data as JSON |
| PUT | `/aplikasi/{id}` | Update application and synchronize attributes |
| DELETE | `/aplikasi/{id}` | Delete application and dependent pivot values |
| GET | `/aplikasi/export` | Stream a formula safe CSV export |

## Dynamic attributes

| Method | Path | Purpose |
| --- | --- | --- |
| GET | `/atribut` | List global definitions and applications |
| POST | `/atribut` | Create a typed global definition |
| PUT | `/atribut/{id}` | Change a compatible definition |
| DELETE | `/atribut/{id}` | Delete a definition and dependent values |
| GET | `/aplikasi/{id}/atribut` | Read all definitions and current values |
| PUT or POST | `/aplikasi/{id}/atribut` | Validate and update typed values |

## Response behavior

- Invalid JSON or modal form commands return HTTP `422` with a Laravel validation error map.
- Missing resources return HTTP `404`.
- Unauthenticated JSON requests return HTTP `401`; browser requests redirect to login.
- Unexpected exceptions are logged by Laravel and return a generic server response. Internal exception text is not included in application responses.
- All state changing browser requests require a valid CSRF token.
