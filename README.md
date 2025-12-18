 
##OneSyntax TDD Test - Kasun

###Project Structure:
- App
  - Application
    - Contracts ``contract belongs in Application``
      - EmailServiceContract.php
    - Listeners
      - SendPostPublished.php
  - Post ``Post domain separated``
    - DataTransferObjects ``SRP - Validation and business logic separated``
      - SubmitPostData.php
    - Entities
      - Post.php
      - PostEmailRecipient.php
      - Subscriber.php
    - Repositories ``Dependency inversion — defines interfaces only, no framework specifics``
      - PostRepositoryInterface.php
    - UseCases ``How business logic implemented``
      - PostSubmitUseCase.php
  - Infrastructure ``Framework-specific implementations``
    - Repositories
      - EloquentPostRepository.php
  - User
  - WebSite
  - Mail
    - PostPublishedMail.php
- Database
  - factories
    - WebsiteFactory.php
  - migrations
    - 001_create_users_table.php
