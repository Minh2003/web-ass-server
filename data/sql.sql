drop schema if exists restaurant;

create schema restaurant;

use restaurant;

create table dish (
	id int auto_increment primary key,
    name varchar(50) not null,
    description varchar(2000) not null,
    image varchar(200) not null
);

create table blog(
	id int auto_increment primary key,
    title varchar(200) not null,
    content varchar(2000) not null,
    image varchar(200) not null,
    date timestamp default now()
);

create table reservation (
	id int auto_increment primary key,
    name varchar(50),
    email varchar(50),
    phoneNumber char(10),
    NoP int,
    date date,
    time time,
    description varchar(2000)
);

create table user (
	id int auto_increment primary key,
	email varchar(50),
    password varchar(72),
	username varchar(50) not null unique,
	phoneNumber varchar(10),
    manager boolean default false,
    avatar varchar(100)
);

create table comment (
	id serial primary key,
    userId int,
    blogId int,
    foreign key(userId) references user(id),
    foreign key(blogId) references blog(id),
    description varchar(1000) not null
);