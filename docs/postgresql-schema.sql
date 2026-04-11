-- PostgreSQL starter DDL aligned to the Laravel migrations.
create extension if not exists "pgcrypto";

create table if not exists guardians (
    id uuid primary key default gen_random_uuid(),
    first_name varchar(100) not null,
    last_name varchar(100) not null,
    phone varchar(30),
    email varchar(255) unique,
    address text,
    relationship varchar(50),
    created_at timestamp null,
    updated_at timestamp null
);

create table if not exists staff (
    id uuid primary key default gen_random_uuid(),
    employee_number varchar(50) not null unique,
    first_name varchar(100) not null,
    last_name varchar(100) not null,
    gender varchar(20),
    phone varchar(30),
    email varchar(255) unique,
    job_title varchar(100),
    hire_date date,
    status varchar(20) not null default 'ACTIVE',
    created_at timestamp null,
    updated_at timestamp null
);

create table if not exists users (
    id uuid primary key default gen_random_uuid(),
    username varchar(100) not null unique,
    password varchar(255) not null,
    staff_id uuid unique references staff(id) on delete set null,
    guardian_id uuid unique references guardians(id) on delete set null,
    api_token varchar(80) unique,
    is_active boolean not null default true,
    last_login_at timestamp null,
    remember_token varchar(100),
    created_at timestamp null,
    updated_at timestamp null
);

-- Remaining tables are implemented in Laravel migration 2026_04_11_000100_create_mis_core_tables.php.
-- Keep PostgreSQL as the primary production target and let Laravel own schema evolution.
