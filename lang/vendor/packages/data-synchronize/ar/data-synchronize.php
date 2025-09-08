<?php

return [
    'tools' => [
        'export_import_data' => 'تصدير/استيراد البيانات',
    ],
    'import' => [
        'name' => 'استيراد',
        'heading' => 'استيراد :label',
        'failed_to_read_file' => 'الملف غير صالح أو تالف أو كبير جدًا بحيث لا يمكن قراءته.',
        'form' => [
            'quick_export_message' => 'إذا كنت ترغب في تصدير بيانات :label، يمكنك القيام بذلك بسرعة من خلال النقر على :export_csv_link أو :export_excel_link.',
            'quick_export_button' => 'تصدير إلى :format',
            'dropzone_message' => 'اسحب الملف هنا أو انقر للتحميل',
            'allowed_extensions' => 'اختر ملفًا بالامتدادات التالية: :extensions.',
            'import_button' => 'استيراد',
            'chunk_size' => 'حجم الجزء',
            'chunk_size_helper' => 'يتم تحديد عدد الصفوف التي سيتم استيرادها في كل مرة من خلال حجم الدفعة. قم بزيادة هذه القيمة إذا كان لديك ملف كبير ويتم استيراد البيانات بسرعة كبيرة. قلل هذه القيمة إذا واجهت حدود الذاكرة أو مشكلات انتهاء مهلة البوابة أثناء استيراد البيانات.',
        ],
        'failures' => [
            'title' => 'الإخفاقات',
            'attribute' => 'سمة',
            'errors' => 'أخطاء',
        ],
        'example' => [
            'title' => 'مثال',
            'download' => 'تحميل ملف :type النموذجي',
        ],
        'rules' => [
            'title' => 'القواعد',
            'column' => 'عمود',
        ],
        'uploading_message' => 'بدء تحميل الملف...',
        'uploaded_message' => 'تم تحميل الملف :file بنجاح. جارٍ التحقق من البيانات...',
        'validating_message' => 'جارٍ التحقق من :from إلى :to...',
        'importing_message' => 'جارٍ الاستيراد من :from إلى :to...',
        'done_message' => 'تم استيراد :count :label بنجاح.',
        'validating_failed_message' => 'فشل التحقق. يرجى مراجعة الأخطاء أدناه.',
        'no_data_message' => 'بياناتك محدثة بالفعل أو لا توجد بيانات للاستيراد.',
    ],
    'export' => [
        'name' => 'تصدير',
        'heading' => 'تصدير :label',
        'form' => [
            'all_columns_disabled' => 'سيتم تصدير الأعمدة التالية: :columns.',
            'columns' => 'أعمدة',
            'format' => 'التنسيق',
            'export_button' => 'تصدير',
        ],
        'success_message' => 'تم التصدير بنجاح.',
        'error_message' => 'فشل التصدير.',
        'empty_state' => [
            'title' => 'لا توجد بيانات للتصدير',
            'description' => 'يبدو أنه لا توجد بيانات للتصدير.',
            'back' => 'Back to :page',
        ],
    ],
    'check_all' => 'Check all',
];
