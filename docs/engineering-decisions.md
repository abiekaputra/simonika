# Engineering decisions and tradeoffs

## Modular monolith

The system uses one Laravel application because the workflows share one relational data model and do not require independent scaling. Splitting them into services would add deployment, consistency, and observability work without solving a demonstrated problem.

## Server rendered interface

Blade and small JavaScript modules keep authentication, routing, and deployment straightforward. A separate SPA would be justified only if offline behavior, a public API, or substantially richer client state becomes a requirement.

## Streaming CSV instead of XLSX

CSV covers the actual export workflow and can be streamed with constant memory through Laravel lazy iteration. It also removes a vulnerable and unnecessary spreadsheet dependency. Exported cells beginning with formula control characters are prefixed to prevent execution in spreadsheet software.

## Global typed attributes

Custom attributes allow the inventory schema to evolve without adding a database column for every local metadata need. The tradeoff is runtime validation and less direct SQL typing. SiMonika addresses that by storing the definition type centrally and validating every value before persistence.

## Client side inventory filtering

The present catalogue is small, so loading application rows makes multi-field filtering immediate and keeps the HTTP interface simple. The boundary is documented: database filtering and pagination are required before using this design for a large inventory.

## Optional application and project relation

A delivery project can point to an inventory application, which connects planning records to the system being changed. The foreign key is nullable because some operational projects concern infrastructure or policy. Application deletion sets the reference to null so it cannot erase project timelines or their historical evidence.

## Synchronous mail

Synchronous mail keeps local setup small and makes delivery failure visible immediately. It also increases request latency and couples account management to the mail provider. A queued, after commit delivery path with retry and monitoring is the preferred production improvement.
