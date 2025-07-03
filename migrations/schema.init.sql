create table if not exists products
(
    id int auto_increment primary key,
    uuid varchar(255) not null comment 'UUID товара',
    category varchar(255) not null comment 'Категория товара',
    is_active tinyint default 1 not null comment 'Флаг активности',
    `name` varchar(255) not null comment 'Название товара',
    description text null comment 'Описание товара',
    thumbnail varchar(255) null comment 'Ссылка на картинку',
    price decimal(10, 2) not null comment 'Цена'
)
    comment 'Товары';

create unique index idx_products_uuid on products (uuid);
create index idx_products_category_active on products (category, is_active);