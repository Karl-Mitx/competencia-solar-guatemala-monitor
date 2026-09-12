<?php

return [
    'required' => 'El campo :attribute es obligatorio.', 'email' => 'Ingresa un correo válido.',
    'integer' => ':attribute debe ser un número entero.', 'numeric' => ':attribute debe ser numérico.',
    'string' => ':attribute debe ser texto.', 'boolean' => ':attribute debe indicar sí o no.',
    'date' => ':attribute debe ser una fecha válida.', 'date_format' => ':attribute debe usar el formato :format.',
    'exists' => 'El valor seleccionado de :attribute no existe.', 'unique' => 'Ya existe un registro con este :attribute.',
    'distinct' => 'No repitas el mismo panel en la instalación.', 'array' => ':attribute debe ser una lista válida.',
    'between' => ['numeric' => ':attribute debe estar entre :min y :max.'],
    'max' => ['string' => ':attribute no puede superar :max caracteres.', 'numeric' => ':attribute no puede superar :max.', 'array' => ':attribute no puede incluir más de :max elementos.'],
    'min' => ['numeric' => ':attribute debe ser al menos :min.'],
    'before' => ':attribute debe corresponder a un mes cerrado, anterior a :date.',
    'before_or_equal' => ':attribute debe ser anterior o igual a :date.',
    'after_or_equal' => ':attribute debe ser posterior o igual a :date.',
    'decimal' => ':attribute admite hasta :decimal decimales.', 'in' => 'El valor de :attribute no es válido.',
    'attributes' => ['name' => 'nombre', 'department_id' => 'departamento', 'solar_farm_id' => 'granja', 'location_name' => 'ubicación', 'latitude' => 'latitud', 'longitude' => 'longitud', 'families_count' => 'familias', 'commissioned_at' => 'inicio de operación', 'nominal_power_kw' => 'potencia nominal', 'brand' => 'marca', 'model' => 'modelo', 'real_kwh' => 'generación real', 'expected_kwh' => 'generación esperada', 'period' => 'período', 'from' => 'mes inicial', 'to' => 'mes final', 'email' => 'correo', 'password' => 'contraseña'],
];
