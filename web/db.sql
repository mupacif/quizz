drop table if exists questions;
create table questions(
id integer primary key autoincrement,
question text not null,
answer text
);

insert into questions(question,answer)
values
("Mon nom?","Pacifique"),
("quel est mon age?","12");