user
    id
    username
    password
    name
    status
    flag_delete
    login_attempt
    last_login_attempt
    created_at
    created_by
    updated_at
    updated_by

create table users (
    id bigserial primary key,
    username varchar(32) not null unique,
    password varchar(512) not null,
    name varchar(128) not null,
    status integer not null,
    flag_delete integer not null,
    login_attempt integer not null,
    last_login_attempt timestamp,
    created_at timestamp not null,
    created_by varchar(128)not null,
    updated_at timestamp,
    updated_by varchar(64),
);

user_attribute
    id
    user_id
    attribute_name
    attribute_value
    created_at
    created_by
    updated_at
    updated_by

create table users_attribute
(
    id              bigserial   primary key,
    users_id        bigint       not null,
    attribute_name  varchar(128) not null,
    attribute_value varchar(512),
    created_by      varchar(64)  not null,
    created_at      timestamp    not null,
    updated_by      varchar(64),
    updated_at      timestamp,
    CONSTRAINT fk_users FOREIGN KEY(users_id) REFERENCES users(id)
);

user_document
    id
    name
    type
        transcript
        krs
        internship_proposal
        internship_report
        final_project_proposal
        final_project_report
    location
    created_at
    created_by
    updated_at
    updated_by

create table users_document
(
    id              bigserial   primary key,
    name            varchar(128)       not null,
    type            varchar(64)     not null,
    location        varchar(512) not null,
    users_id        bigint       not null,
    created_by      varchar(64)  not null,
    created_at      timestamp    not null,
    updated_by      varchar(64),
    updated_at      timestamp,
    CONSTRAINT fk_users FOREIGN KEY(users_id) REFERENCES users(id)
);

internship
    id
    title
    description
    company
    start_date
    end_date
    proposal_id
    transcript_id
    krs_id
    student_id
    coordinator_id
    mentor_id
    status
        0 submit
        1 approve
        2 deny
    created_at
    created_by
    updated_at
    updated_by

create table internship
(
    id              bigserial   primary key,
    title           varchar(512) not null,
    description     text,
    company_name    varchar(128) not null,
    company_address varchar(128) not null,
    company_phone   varchar(128) not null,
    start_date      timestamp    not null,
    end_date        timestamp    not null,
    student_id      bigint       not null,
    mentor_id       bigint,
    transcript_id   bigint       not null,
    krs_id          bigint       not null,
    proposal_id     bigint,
    status          integer     DEFAULT 0 not null,
    created_by      varchar(64)  not null,
    created_at      timestamp    not null,
    updated_by      varchar(64),
    updated_at      timestamp
);

final_project
    id
    title
    description
    proposal
    student
    coordinator
    mentor
    status
        0 submit
        1 approve
        2 deny
    created_at
    created_by
    updated_at
    updated_by

notification
    id
    type
        deny
        approve
    title
    message
    entity
        internship
        final_project
    entity_id
    created_at
    created_by
    updated_at
    updated_by

create table notification
(
    id              bigserial   primary key,
    type            varchar(64) not null,
    title           varchar(128) not null,
    message         text,
    entity          varchar(128) not null,
    entity_id       bigint not null,
    users_id        bigint not null,
    created_by      varchar(64)  not null,
    created_at      timestamp    not null,
    updated_by      varchar(64),
    updated_at      timestamp,
    CONSTRAINT fk_users FOREIGN KEY(users_id) REFERENCES users(id)
);