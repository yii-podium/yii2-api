# Yii 2 Podium API Docs

 - [Installation](en/installation.md)

## Components

 - [Account](en/account.md)
 - [Category](en/category.md)
 - [Forum](en/forum.md)
 - [Group](en/group.md)
 - [Log](en/log.md)
 - [Member](en/member.md)
 - [Message](en/message.md)
 - [Post](en/post.md)
 - [Rank](en/rank.md)
 - [Thread](en/thread.md)

Podium is an [Yii 2](https://www.yiiframework.com/) forum [module](https://www.yiiframework.com/doc/guide/2.0/en/structure-modules). 
This very package is only the Podium API, so if you are looking for a full working forum experience head over to 
[Yii 2 Podium Web Client]() or [Yii 2 Podium Rest Client](). On top of everything this package is abstract in terms of 
repository storage, which means that there are no implementations of repository interfaces - if you are looking for 
Active Record database storage go to [Yii 2 Active Record Podium API]().

Podium is divided into components (not to be mistaken by Yii's [components](https://www.yiiframework.com/doc/guide/2.0/en/structure-application-components) 
although these are implemented as them) that take care of main aspects of forum structure. Each component is responsible 
for actions (again, not Yii's [actions](https://www.yiiframework.com/doc/guide/2.0/en/structure-controllers#actions)) 
concerning its aspect, and these actions are implemented as services that operate on repositories. As for the 
repositories - these are objects that know about the storage of data they can handle and how to work with them.

Podium data structure is as following:

 1. Categories.
 2. Forums.
 3. Threads.
 4. Posts.
