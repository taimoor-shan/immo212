<?php

return [
    'name' => 'Bienes Raíces',
    'settings' => 'Configuración',
    'login_form' => 'Formulario de inicio de sesión',
    'register_form' => 'Formulario de registro',
    'forgot_password_form' => 'Formulario de recuperación de contraseña',
    'reset_password_form' => 'Formulario para restablecer la contraseña',
    'consult_form' => 'Formulario de consulta',
    'review_form' => 'Formulario de revisión',
    'theme_options' => [
        'slug_name' => 'URLs inmobiliarios',
        'slug_description' => 'Personalice los slugs utilizados para las páginas inmobiliarias. Tenga cuidado al modificarlos, ya que puede afectar el SEO y la experiencia del usuario. Si ocurre algún problema, puede restablecer los valores predeterminados escribiendo el valor por defecto o dejándolo en blanco.',
        'page_slug_name' => ':página slug de la página',
        'page_slug_description' => 'Se verá como :slug cuando acceda a la página. El valor predeterminado es :default.',
        'page_slug_already_exists' => 'El slug de página :slug ya está en uso. Por favor, elija otro.',
        'page_slugs' => [
            'projects_city' => 'Proyectos por ciudad',
            'projects_state' => 'Proyectos por estado',
            'properties_city' => 'Propiedades por ciudad',
            'properties_state' => 'Propiedades por estado',
        ],
    ],
];
