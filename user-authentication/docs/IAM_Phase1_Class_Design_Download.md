# IAM Phase 1 — PHP Class Design

## Scope and assumptions

This design follows the supplied Phase 1 ERD: users, password credentials, sessions, rotating refresh tokens, one-time tokens, roles, permissions, user-role assignments, role-permission assignments, and append-only audit events.

Assumptions for the examples:
- PHP 8.2+ (so PHP enums and typed properties are available).
- IDs are UUIDs represented as strings in PHP.
- Database timestamps are mapped to `DateTimeImmutable`.
- This is an ORM-neutral design; it can be implemented with PDO first or mapped to an ORM later.
- `user_roles` and `role_permissions` use the composite primary keys shown in the ERD, so their classes do not have separate `id` fields.

## 1. Class diagram

```mermaid
classDiagram
 direction TB

 class User {
   +string id
   +string email
   +?DateTimeImmutable emailVerifiedAt
   +?string displayName
   +UserStatus status
   +DateTimeImmutable createdAt
   +DateTimeImmutable updatedAt
   +?DateTimeImmutable deletedAt
   +isActive() bool
   +markEmailVerified(at) void
   +suspend() void
   +softDelete(at) void
 }
 class PasswordCredential {
   +string userId
   +string passwordHash
   +DateTimeImmutable changedAt
   +bool mustChange
   +changePassword(hash, at) void
 }
 class Session {
   +string id
   +string userId
   +string tokenHash
   +string[] authMethods
   +?string ipAddress
   +?string userAgent
   +DateTimeImmutable createdAt
   +DateTimeImmutable lastSeenAt
   +DateTimeImmutable expiresAt
   +?DateTimeImmutable revokedAt
   +?string revokedReason
   +isUsableAt(now) bool
   +touch(at) void
   +revoke(reason, at) void
 }
 class RefreshToken {
   +string id
   +string sessionId
   +string tokenHash
   +DateTimeImmutable issuedAt
   +DateTimeImmutable expiresAt
   +?DateTimeImmutable usedAt
   +?string replacedById
   +isExpiredAt(now) bool
   +markUsed(at) void
   +replaceWith(newTokenId, at) void
 }
 class OneTimeToken {
   +string id
   +string userId
   +TokenPurpose purpose
   +string tokenHash
   +DateTimeImmutable expiresAt
   +?DateTimeImmutable consumedAt
   +isValidAt(now) bool
   +consume(at) void
 }
 class Role {
   +string id
   +string name
   +?string description
   +bool isSystem
 }
 class Permission {
   +string id
   +string resource
   +string action
 }
 class UserRole {
   +string userId
   +string roleId
   +?string grantedBy
   +DateTimeImmutable grantedAt
   +?DateTimeImmutable expiresAt
   +isEffectiveAt(at) bool
 }
 class RolePermission {
   +string roleId
   +string permissionId
 }
 class AuditEvent {
   +string id
   +DateTimeImmutable occurredAt
   +string eventType
   +string outcome
   +string actorType
   +?string actorId
   +?string subjectUserId
   +?string ipAddress
   +?string requestId
   +array details
 }
 class UserStatus {
   <<enumeration>>
   Pending
   Active
   Suspended
   Deleted
 }
 class TokenPurpose {
   <<enumeration>>
   EmailVerify
   PasswordReset
 }

 User "1" *-- "0..1" PasswordCredential : credential
 User "1" --> "0..*" Session : opens
 Session "1" *-- "0..*" RefreshToken : token family
 User "1" --> "0..*" OneTimeToken : requests
 User "1" --> "0..*" UserRole : receives roles
 Role "1" --> "0..*" UserRole : assigned through
 Role "1" --> "0..*" RolePermission : includes
 Permission "1" --> "0..*" RolePermission : granted through
 RefreshToken "0..1" --> "0..1" RefreshToken : replacedBy
 User ..> UserStatus : uses
 OneTimeToken ..> TokenPurpose : uses
 AuditEvent ..> User : actor/subject IDs only; no FK
```

## 2. Entity definitions

The entity class names are singular and PascalCase. They map to the plural snake_case tables in the ERD.

| PHP class | Database table | Main properties | Responsibility / useful behavior |
|---|---|---|---|
| `User` | `users` | `id`, `email`, `emailVerifiedAt`, `displayName`, `status`, `createdAt`, `updatedAt`, `deletedAt` | Represents identity and account state. Methods: `isActive()`, `markEmailVerified($at)`, `suspend()`, `softDelete($at)`. Keep email uniqueness in the database as well as validating at the application boundary. |
| `PasswordCredential` | `password_credentials` | `userId`, `passwordHash`, `changedAt`, `mustChange` | Stores the password authentication method separately from the identity. Methods: `changePassword($hash, $at)` and optionally `requirePasswordChange()`. Store only the password hash (Argon2id PHC string), never the clear-text password. |
| `Session` | `sessions` | `id`, `userId`, `tokenHash`, `authMethods`, `ipAddress`, `userAgent`, `createdAt`, `lastSeenAt`, `expiresAt`, `revokedAt`, `revokedReason` | Represents a logged-in session/device. Methods: `isUsableAt($now)`, `touch($at)`, `revoke($reason, $at)`. Check expiry and revocation whenever the session is used. |
| `RefreshToken` | `refresh_tokens` | `id`, `sessionId`, `tokenHash`, `issuedAt`, `expiresAt`, `usedAt`, `replacedById` | Tracks one opaque refresh token in a session's token family. Methods: `isExpiredAt($now)`, `markUsed($at)`, `replaceWith($newTokenId, $at)`. Persist a hash, not the raw token. Rotation/reuse detection belongs in a service that coordinates database updates transactionally. |
| `OneTimeToken` | `one_time_tokens` | `id`, `userId`, `purpose`, `tokenHash`, `expiresAt`, `consumedAt` | Represents a single-use email-verification or password-reset token. Methods: `isValidAt($now)`, `consume($at)`. A token is valid only if it is unconsumed and unexpired. |
| `Role` | `roles` | `id`, `name`, `description`, `isSystem` | Represents a named group of permissions, such as `admin` or `editor`. System roles should not be casually renamed or deleted. |
| `Permission` | `permissions` | `id`, `resource`, `action` | Represents one capability, such as resource `users` + action `read`. The ERD makes `(resource, action)` unique. |
| `UserRole` | `user_roles` | `userId`, `roleId`, `grantedBy`, `grantedAt`, `expiresAt` | Join entity for a user's role assignment, including who granted it and optional expiry. Method: `isEffectiveAt($at)`. Primary key is `(userId, roleId)`. `grantedBy` is a logical reference in the supplied ERD, not an enforced FK. |
| `RolePermission` | `role_permissions` | `roleId`, `permissionId` | Join entity linking a role to a permission. Primary key is `(roleId, permissionId)`. The uniqueness of the pair prevents duplicate grants. |
| `AuditEvent` | `audit_events` | `id`, `occurredAt`, `eventType`, `outcome`, `actorType`, `actorId`, `subjectUserId`, `ipAddress`, `requestId`, `details` | Immutable, append-only security/audit record. Do not expose general update/delete methods. `actorId` and `subjectUserId` are intentionally logical IDs without foreign keys in the ERD. `details` must never contain passwords, raw tokens, or other secrets. |

### Enums / constrained values

```php
<?php
declare(strict_types=1);

namespace App\IAM\Domain;

enum UserStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Deleted = 'deleted';
}

enum TokenPurpose: string
{
    case EmailVerify = 'email_verify';
    case PasswordReset = 'password_reset';
}
```

Use the actual values configured in the database constraints. Do not let arbitrary strings enter these fields if the set of valid values is fixed.

## 3. Service classes and interfaces (not database tables)

Keep business workflows out of controllers and avoid putting SQL inside entities. These application-level classes coordinate entity behavior and persistence.

| Class / interface | Responsibility | Suggested starting methods |
|---|---|---|
| `AuthenticationService` | Register a user and verify credentials during login. | `register(email, password)`, `authenticate(email, password)` |
| `SessionService` | Create sessions, validate current sessions, update last-seen time, and revoke sessions. | `createForUser(user, authMethods, requestContext)`, `revoke(sessionId, reason)`, `validate(sessionToken)` |
| `RefreshTokenService` | Rotate refresh tokens and detect reuse. Must wrap related writes in a transaction. | `rotate(rawRefreshToken)`, `revokeFamily(sessionId, reason)` |
| `OneTimeTokenService` | Issue and consume email-verification/password-reset tokens. | `issue(userId, purpose)`, `consume(rawToken, purpose)` |
| `AccessControlService` | Determine whether a user has a requested `resource` + `action` permission using effective role assignments. | `hasPermission(userId, resource, action)`, `getPermissionsForUser(userId)` |
| `AuditLogger` | Write security-relevant events, preferably in the same transaction when audit atomicity is required. | `record(event)` |
| `PasswordHasher` (interface) | Abstracts password hashing and verification; implementation uses PHP's password APIs with Argon2id where available. | `hash(plainText)`, `verify(plainText, hash)` |
| `TokenHasher` (interface) | Hashes opaque session/refresh/one-time token values for storage. | `hash(rawToken)` |
| `UserRepository` (interface) | Loads and persists users; database/ORM code stays outside domain entities. | `findById(id)`, `findByEmail(email)`, `save(user)` |
| `PasswordCredentialRepository` (interface) | Loads and persists a user's password credential. | `findByUserId(userId)`, `save(credential)` |
| `SessionRepository` (interface) | Persists and queries sessions. | `findById(id)`, `findByTokenHash(hash)`, `save(session)` |
| `RefreshTokenRepository` (interface) | Persists refresh tokens and supports lookups needed for rotation/reuse detection. | `findByTokenHash(hash)`, `save(token)` |
| `OneTimeTokenRepository` (interface) | Finds and persists one-time tokens. | `findByTokenHashAndPurpose(hash, purpose)`, `save(token)` |
| `AccessControlRepository` (interface) | Reads user-role, role-permission, role, and permission data. | `userHasPermission(userId, resource, action, at)` |
| `AuditEventRepository` (interface) | Appends audit events. It should not expose normal update/delete operations. | `append(event)` |

Repositories are interfaces/abstractions. Their concrete implementations might be `PdoUserRepository`, `PdoSessionRepository`, etc. if you use PDO. They are not extra tables.

## 4. Important relationships and implementation rules

1. **User–PasswordCredential is one-to-zero-or-one.** The credential uses `user_id` as both PK and FK. A user can exist without a password credential if a later authentication method is introduced.
2. **User–Session is one-to-many.** A user can have multiple sessions. Revoking one session should not automatically revoke every other session unless your policy explicitly says so.
3. **Session–RefreshToken is one-to-many.** A session groups its refresh-token family. Rotation and replay detection are workflows, not just entity methods. `replacedById` is a nullable self-reference. If your intended invariant is that a replacement token can have only one predecessor, add a unique constraint on `replaced_by_id`; that uniqueness is not shown in the supplied ERD.
4. **User–Role is many-to-many through `UserRole`.** The join entity holds grant metadata and expiry, so do not model it as only a bare array of role IDs.
5. **Role–Permission is many-to-many through `RolePermission`.** Permissions are capabilities; roles group capabilities.
6. **AuditEvent has logical user references only.** Keep `actorId` and `subjectUserId` as identifiers unless you intentionally change the ERD to add FK constraints.
7. **Never store raw bearer/session/refresh/one-time tokens.** Return the raw token to the client once; store only the hash. Passwords use a password-specific hashing algorithm, not the same simple token hash.
8. **Use transactions for sensitive multi-row workflows.** In particular, refresh-token rotation and consuming a password-reset token while changing the password must be atomic.
9. **Enforce invariants in both application code and the database.** Unique email/token hashes, unique `(resource, action)`, unique role names, and composite join-table keys must be backed by database constraints.
10. **Audit is append-only.** Avoid updating existing audit rows. Partitioning and retention are database concerns and can be added without changing the main domain model much.

## 5. Suggested folder structure

```text
src/
  IAM/
    Domain/
      User.php
      PasswordCredential.php
      Session.php
      RefreshToken.php
      OneTimeToken.php
      Role.php
      Permission.php
      UserRole.php
      RolePermission.php
      AuditEvent.php
      UserStatus.php
      TokenPurpose.php
    Application/
      AuthenticationService.php
      SessionService.php
      RefreshTokenService.php
      OneTimeTokenService.php
      AccessControlService.php
      AuditLogger.php
    Contracts/
      UserRepository.php
      PasswordCredentialRepository.php
      SessionRepository.php
      RefreshTokenRepository.php
      OneTimeTokenRepository.php
      AccessControlRepository.php
      AuditEventRepository.php
      PasswordHasher.php
      TokenHasher.php
    Infrastructure/
      Persistence/   # PDO or ORM implementations
      Security/      # password/token hasher implementations
    Http/            # controllers / request validation
```

## 6. Recommended implementation order

1. Create `UserStatus`, `TokenPurpose`, `User`, and `PasswordCredential`.
2. Add `PasswordHasher` and the registration/login workflow.
3. Add `Session` and `SessionService`.
4. Add `RefreshToken` and rotation/reuse detection.
5. Add `OneTimeToken` and verification/reset flows.
6. Add roles/permissions and `AccessControlService`.
7. Add `AuditEvent` and wire audit records into security workflows.

This order makes it possible to test one working authentication path before adding the remaining features.
