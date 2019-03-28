create table user (
user_id int unsigned auto_increment primary key
);

create table messages (
message_id int unsigned auto_increment primary key,
message varchar(255);
user_id int unsigned,
foreign key (user_id) references user(user_id);
);
