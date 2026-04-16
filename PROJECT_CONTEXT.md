cat > PROJECT_CONTEXT.md << 'EOF'
# Контекст проекта "Спутник-Искож"

## Основные возможности
- CRM для предприятия садоводческого товарищества
- Формы обращений и обратной связи
- Ведение финансовой отчётности
- Генерация документов (PDF, QR-коды)
- Экспорт в Excel (maatwebsite/excel)
- Диаграммы (chart.js)
- WYSIWYG редакторы (TinyMCE, TipTap, Quill)

## Важные пакеты
- spatie/laravel-pdf — генерация PDF
- dompdf — альтернативный PDF
- simplesoftwareio/simple-qrcode — QR-коды
- cknow/laravel-money — работа с деньгами

## Аутентификация
- Laravel Sanctum (API токены)
- Возможно, используется laravel/ui (Bootstrap стили)

## Среда разработки
- Используется Sail? (в require-dev есть laravel/sail)
- Если да, то все команды через `./vendor/bin/sail`
  EOF