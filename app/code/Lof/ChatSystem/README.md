# MAGENTO 2 CHAT SYSTEM

    ``landofcoder/module-chat-system``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)

## Main Functionalities
https://landofcoder.com/magento-2-live-chat-extension.html

Magento 2 Live Chat Extension FREE help you chat directly with customers and clear their doubts about all the product’s aspects.

The higher levels of satisfaction with online chat are partially due to the efficiency and immediacy of the experience in your site.

Let’s look at some powerful features of Magento 2 Live Chat Extension:

- Directly Online Chatting
- Convenient Login & Signup
- Clean & Clear Interface
- Flexible Join & End Chatbox
- Reminding Chat Notification
- Auto Record Customer Information
- Instant Chat Box
- Actively Chatting With Desired Customers

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Lof`
 - Enable the module by running `php bin/magento module:enable Lof_ChatSystem`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require landofcoder/module-chat-system`
 - enable the module by running `php bin/magento module:enable Lof_ChatSystem`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`
