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
    company_name
    company_address
    company_phone
    start_date
    end_date
    statement_id
    proposal_id
    transcript_id
    krs_id
    student_id
    supervisor_id
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
    supervisor_id       bigint,
    transcript_id   bigint       not null,
    krs_id          bigint       not null,
    statement_id     bigint     not null,
    status          integer     DEFAULT 0 not null,
    created_by      varchar(64)  not null,
    created_at      timestamp    not null,
    updated_by      varchar(64),
    updated_at      timestamp,
    CONSTRAINT fk_student FOREIGN KEY(student_id) REFERENCES users(id),
    CONSTRAINT fk_transcript FOREIGN KEY(transcript_id) REFERENCES users_document(id),
    CONSTRAINT fk_krs FOREIGN KEY(krs_id) REFERENCES users_document(id),
    CONSTRAINT fk_statement FOREIGN KEY(statement_id) REFERENCES users_document(id),
);

create table internship_seminar
(
    id              bigserial   primary key,
    internship_id   bigint  not null,
    -- title           varchar(512) not null,
    -- description     text,
    -- company_name    varchar(128) not null,
    -- company_address varchar(128) not null,
    -- company_phone   varchar(128) not null,
    -- start_date      timestamp    not null,
    -- end_date        timestamp    not null,
    -- student_id      bigint       not null,
    -- mentor_id       bigint       not null,
    -- transcript_id   bigint       not null,
    -- krs_id          bigint       not null,
    registration_id bigint       not null,
    report_id       bigint       not null,
    assessment_sheet_id bigint      not null,
    schedule        timestamp,
    status          integer     DEFAULT 0 not null,
    created_by      varchar(64)  not null,
    created_at      timestamp    not null,
    updated_by      varchar(64),
    updated_at      timestamp,
    CONSTRAINT fk_internsip FOREIGN KEY(internship_id) REFERENCES internship(id),
    CONSTRAINT fk_registration FOREIGN KEY(registration_id) REFERENCES users_document(id),
    CONSTRAINT fk_report FOREIGN KEY(report_id) REFERENCES users_document(id),
    CONSTRAINT fk_assessment FOREIGN KEY(assessment_sheet_id) REFERENCES users_document(id),
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

create table final_project
(
    id              bigserial   primary key,
    title           varchar(512) not null,
    description     text,
    student_id      bigint       not null,
    mentor1_id       bigint,
    mentor2_id       bigint,
    transcript_id   bigint       not null,
    krs_id          bigint       not null,
    proposal_id     bigint,
    status          integer     DEFAULT 0 not null,
    created_by      varchar(64)  not null,
    created_at      timestamp    not null,
    updated_by      varchar(64),
    updated_at      timestamp
);

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

create table final_project_seminar
(
    id              bigserial   primary key,
    title           varchar(512) not null,
    description     text,
    student_id      bigint       not null,
    mentor1_id       bigint,
    mentor2_id       bigint,
    transcript_id   bigint       not null,
    krs_id          bigint       not null,
    registration_id bigint       not null,
    report_id     bigint not null,
    examiner1_id     bigint,
    examiner2_id     bigint,
    examiner3_id     bigint,
    schedule        timestamp,
    status          integer     DEFAULT 0 not null,
    created_by      varchar(64)  not null,
    created_at      timestamp    not null,
    updated_by      varchar(64),
    updated_at      timestamp
);