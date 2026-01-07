CREATE DATABASE Athena;

USE Athena;

CREATE TABLE user (
    id INT primary key auto_increment,
    full_name varchar(255) not null,
    email varchar(255) not null unique,
    password varchar(255) not null,
    role enum ("admin", "scrum", "member") default "member",
    created_at timestamp default current_timestamp
);

CREATE TABLE projects (
    id int primary key auto_increment,
    project_name varchar(255) not null,
    description text,
    created_by int not null,
    start_date date,
    end_date date,
    status enum ("in_progress", "finished", "pending") default "in_progress",
    created_at timestamp default current_timestamp,
    foreign key (created_by) references user(id) on delete cascade
);

CREATE TABLE sprints (
    id int primary key auto_increment,
    sprint_name varchar(255) not null,
    start_date date,
    end_date date,
    status enum ("in_progress", "finished", "pending") default "in_progress",
    created_at timestamp default current_timestamp,
    project_id int,
    foreign key (project_id) references projects(id) on delete cascade
);

CREATE TABLE tasks (
    id int primary key auto_increment,
    task_title varchar(255) not null,
    description text,
    assigned_to int,
    priority enum ("low", "medium", "high") default "medium",
    status enum ("to_do", "in_progress", "finished") default "to_do",
    created_at timestamp default current_timestamp,
    sprint_id int,
    foreign key (sprint_id) references sprints(id) on delete cascade,
    foreign key (assigned_to) references user(id) on delete set null
);

CREATE TABLE comments (
    id int primary key auto_increment,
    content text not null,
    created_at timestamp default current_timestamp,
    task_id int,
    foreign key (task_id) references tasks(id) on delete cascade,
    user_id int,
    foreign key (user_id) references user(id) on delete cascade
);

CREATE TABLE notifications (
    id int AUTO_INCREMENT PRIMARY KEY,
    user_id int NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

