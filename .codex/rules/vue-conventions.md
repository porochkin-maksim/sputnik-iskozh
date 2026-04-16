# Vue 3 + Vite Conventions

## Стек
- Vue 3 с Composition API (предпочтительно `<script setup>`)
- Vuex 4 для управления состоянием
- Vite для сборки
- Bootstrap 5 (css + js)
- Sass для стилей

## Компоненты
- Компоненты хранятся в `resources/js/components/`
- Используй однофайловые компоненты `.vue`
- Именование: PascalCase для файлов и компонентов

## Роутинг
- Vue Router не указан в package.json — вероятно, используется серверный рендеринг Laravel. Если нужен клиентский роутинг, добавь.

## Редакторы текста
- В проекте есть TinyMCE, TipTap, Quill — используй их согласно задаче.

## Стили
- Основной CSS через Bootstrap 5
- Кастомные стили в `resources/sass/`
- Используй Vite для компиляции Sass