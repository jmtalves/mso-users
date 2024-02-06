create database if not exists users;
use users;
create table `user` (
iduser bigint not null auto_increment,
`name` varchar(255) null,
`email` varchar(200) not null,
`password` varchar(255) not null,
`apikey` varchar(255) not null,
`type` tinyint not null DEFAULT 1 comment '0=>admin,1=>normal user',
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP not null ,
`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP  not null ON UPDATE CURRENT_TIMESTAMP ,
primary key (iduser),
unique (email)
);
alter table user  add unique index apikey_unique (apikey);
INSERT INTO `user` VALUES (1,'Admin','admin@admin.pt','$2y$10$lwfNAgqqfAGAdz1Fif/PuuuXOCEc/giumpYAdHBazFKRHC9BlcL0W','a4b728c805a50b7d81115ce5d10a39d8',0,'2023-10-28 18:51:49','2023-10-28 18:53:02');

 