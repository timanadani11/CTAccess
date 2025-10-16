// ==UserScript==
// @name         CTAccess - Autofill Formulario Personas
// @namespace    http://tampermonkey.net/
// @version      1.2
// @description  Auto-rellena el formulario de personas con ejemplos
// @author       CTAccess
// @match        http://127.0.0.1:8000/personas/create
// @match        http://127.0.0.1:8000/personas/create*
// @icon         data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==
// @grant        none
// @run-at       document-idle
// ==/UserScript==

(function() {
    'use strict';

    alert('🤖 Script Tampermonkey FUNCIONANDO!');
    console.log('🔍 Script Tampermonkey cargado');

    // Datos de ejemplo
    const ejemplos = [
        {
            nombre: "Juan Carlos Rodríguez",
            documento: "1045678901",
            tipoPersona: "Aprendiz",
            correo: "juan.rodriguez@example.com",
            portatiles: [
                { serial: "DLXYZ123456", marca: "Dell", modelo: "Inspiron 15 3000" }
            ],
            vehiculos: [
                { tipo: "Motocicleta", placa: "ABC-123" }
            ]
        },
        {
            nombre: "María Elena Gómez",
            documento: "1034567890",
            tipoPersona: "Instructor",
            correo: "maria.gomez@example.com",
            portatiles: [
                { serial: "HPXYZ789012", marca: "HP", modelo: "Pavilion 14" }
            ],
            vehiculos: []
        }
    ];

    // Función para llenar un input y disparar eventos
    function llenarInput(selector, valor) {
        const input = document.querySelector(selector);
        if (input) {
            input.value = valor;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            input.dispatchEvent(new Event('blur', { bubbles: true }));
        }
    }

    // Función para resetear el formulario al paso 1
    function resetearFormulario() {
        console.log('🔄 Regresando al paso 1...');
        // Buscar y hacer clic en todos los botones "Anterior" hasta llegar al inicio
        const intervalo = setInterval(() => {
            const botones = Array.from(document.querySelectorAll('button'));
            const botonAnterior = botones.find(btn => btn.textContent.includes('Anterior'));

            if (botonAnterior) {
                botonAnterior.click();
            } else {
                // Ya estamos en el paso 1
                clearInterval(intervalo);
                console.log('✅ Formulario en el paso 1');
            }
        }, 300);

        // Timeout de seguridad
        setTimeout(() => clearInterval(intervalo), 3000);
    }

    // Función principal de llenado
    async function llenarFormulario(datos) {
        console.log('🚀 Iniciando llenado automático...', datos);

        // Primero resetear al paso 1
        resetearFormulario();
        await new Promise(resolve => setTimeout(resolve, 1500));

        // PASO 1: Información Personal
        await new Promise(resolve => setTimeout(resolve, 500));

        llenarInput('#nombre', datos.nombre);
        await new Promise(resolve => setTimeout(resolve, 300));

        llenarInput('#documento', datos.documento);
        await new Promise(resolve => setTimeout(resolve, 300));

        llenarInput('#tipoPersona', datos.tipoPersona);
        await new Promise(resolve => setTimeout(resolve, 300));

        llenarInput('#correo', datos.correo);
        await new Promise(resolve => setTimeout(resolve, 500));

        console.log('✅ Paso 1 completado - Información Personal');

        // Buscar botón "Siguiente"
        const botones = Array.from(document.querySelectorAll('button'));
        const botonSiguiente = botones.find(btn => btn.textContent.includes('Siguiente'));

        if (botonSiguiente) {
            botonSiguiente.click();
            await new Promise(resolve => setTimeout(resolve, 800));
        }

        // PASO 2: Portátiles
        console.log('📦 Paso 2 - Portátiles');
        if (datos.portatiles.length > 0) {
            const botonesAgregar = Array.from(document.querySelectorAll('button'));
            const botonAgregarPortatil = botonesAgregar.find(btn =>
                btn.textContent.includes('Agregar Primer Portátil') ||
                btn.textContent.includes('Agregar Otro Portátil')
            );

            if (botonAgregarPortatil) {
                botonAgregarPortatil.click();
                await new Promise(resolve => setTimeout(resolve, 800));
            }

            for (let i = 0; i < datos.portatiles.length; i++) {
                const portatil = datos.portatiles[i];

                const serialInput = document.querySelector('input[placeholder="Ej: ABC123456DEF"]');
                const marcaInput = document.querySelector('input[placeholder="Dell, HP, Lenovo..."]');
                const modeloInput = document.querySelector('input[placeholder="Inspiron 15, ThinkPad X1..."]');

                if (serialInput && marcaInput && modeloInput) {
                    serialInput.value = portatil.serial;
                    serialInput.dispatchEvent(new Event('input', { bubbles: true }));
                    await new Promise(resolve => setTimeout(resolve, 300));

                    marcaInput.value = portatil.marca;
                    marcaInput.dispatchEvent(new Event('input', { bubbles: true }));
                    await new Promise(resolve => setTimeout(resolve, 300));

                    modeloInput.value = portatil.modelo;
                    modeloInput.dispatchEvent(new Event('input', { bubbles: true }));
                    await new Promise(resolve => setTimeout(resolve, 500));

                    console.log(`✅ Portátil ${i + 1} agregado`);
                }
            }
        }

        // Siguiente paso
        const botonesPaso2 = Array.from(document.querySelectorAll('button'));
        const botonSiguiente2 = botonesPaso2.find(btn => btn.textContent.includes('Siguiente'));
        if (botonSiguiente2) {
            botonSiguiente2.click();
            await new Promise(resolve => setTimeout(resolve, 800));
        }

        // PASO 3: Vehículos
        console.log('🚗 Paso 3 - Vehículos');
        if (datos.vehiculos.length > 0) {
            const botonesAgregarVehiculo = Array.from(document.querySelectorAll('button'));
            const botonAgregarVehiculo = botonesAgregarVehiculo.find(btn =>
                btn.textContent.includes('Agregar Primer Vehículo') ||
                btn.textContent.includes('Agregar Otro Vehículo')
            );

            if (botonAgregarVehiculo) {
                botonAgregarVehiculo.click();
                await new Promise(resolve => setTimeout(resolve, 800));
            }

            for (let i = 0; i < datos.vehiculos.length; i++) {
                const vehiculo = datos.vehiculos[i];

                const selectTipo = document.querySelector('select[required]');
                if (selectTipo) {
                    selectTipo.value = vehiculo.tipo;
                    selectTipo.dispatchEvent(new Event('change', { bubbles: true }));
                    selectTipo.dispatchEvent(new Event('input', { bubbles: true }));
                    await new Promise(resolve => setTimeout(resolve, 400));
                }

                const inputPlaca = document.querySelector('input[placeholder="ABC-123"]');
                if (inputPlaca) {
                    inputPlaca.value = vehiculo.placa;
                    inputPlaca.dispatchEvent(new Event('input', { bubbles: true }));
                    await new Promise(resolve => setTimeout(resolve, 500));

                    console.log(`✅ Vehículo ${i + 1} agregado`);
                }
            }
        }

        // Siguiente a resumen
        const botonesPaso3 = Array.from(document.querySelectorAll('button'));
        const botonSiguiente3 = botonesPaso3.find(btn => btn.textContent.includes('Siguiente'));
        if (botonSiguiente3) {
            botonSiguiente3.click();
            await new Promise(resolve => setTimeout(resolve, 800));
        }

        console.log('✅ Formulario completado - Paso 4 (Resumen)');

        // PASO 4: Hacer clic en "Crear Persona"
        await new Promise(resolve => setTimeout(resolve, 1000));
        const botonesFinales = Array.from(document.querySelectorAll('button'));
        const botonCrear = botonesFinales.find(btn =>
            btn.textContent.includes('Crear Persona') ||
            btn.textContent.includes('Guardar')
        );

        if (botonCrear) {
            console.log('🎉 Haciendo clic en "Crear Persona"...');
            botonCrear.click();
            await new Promise(resolve => setTimeout(resolve, 500));
            console.log('✅ Persona creada exitosamente');
        } else {
            console.log('⚠️ No se encontró el botón "Crear Persona"');
        }
    }

    // Crear botones de control
    function crearBotones() {
        console.log('📦 Creando botones...');

        // Verificar si ya existen
        if (document.getElementById('tampermonkey-controls')) {
            console.log('⚠️ Botones ya existen');
            return;
        }

        const contenedor = document.createElement('div');
        contenedor.id = 'tampermonkey-controls';
        contenedor.style.cssText = `
            position: fixed !important;
            bottom: 20px !important;
            right: 20px !important;
            z-index: 999999 !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            padding: 15px !important;
            border-radius: 12px !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5) !important;
            font-family: Arial, sans-serif !important;
            width: 220px !important;
        `;

        contenedor.innerHTML = `
            <div style="color: white; font-weight: bold; margin-bottom: 10px; text-align: center; font-size: 14px;">
                🤖 Auto-Fill CTAccess
            </div>
            <button id="ejemplo1-btn" style="
                width: 100%;
                padding: 12px;
                margin-bottom: 8px;
                background: linear-gradient(135deg, #39A900, #2d7a00);
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: bold;
                transition: all 0.3s;
                font-size: 13px;
            ">
                🏍️ Ejemplo 1<br><small style="font-weight: normal;">Portátil + Motocicleta</small>
            </button>
            <button id="ejemplo2-btn" style="
                width: 100%;
                padding: 12px;
                background: linear-gradient(135deg, #50E5F9, #00B4D8);
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: bold;
                transition: all 0.3s;
                font-size: 13px;
            ">
                💻 Ejemplo 2<br><small style="font-weight: normal;">Solo Portátil</small>
            </button>
        `;

        document.body.appendChild(contenedor);
        console.log('✅ Botones creados exitosamente');

        // Agregar eventos hover
        const botones = contenedor.querySelectorAll('button');
        botones.forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                btn.style.transform = 'scale(1.05)';
                btn.style.boxShadow = '0 6px 16px rgba(0,0,0,0.4)';
            });
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'scale(1)';
                btn.style.boxShadow = 'none';
            });
        });

        // Eventos de clic
        document.getElementById('ejemplo1-btn').addEventListener('click', () => {
            console.log('🏍️ Cargando Ejemplo 1: Portátil + Motocicleta');
            llenarFormulario(ejemplos[0]);
        });

        document.getElementById('ejemplo2-btn').addEventListener('click', () => {
            console.log('💻 Cargando Ejemplo 2: Solo Portátil');
            llenarFormulario(ejemplos[1]);
        });
    }

    // Esperar a que el DOM esté listo
    function inicializar() {
        console.log('🔄 Inicializando...');

        // Esperar a que Vue renderice el formulario
        let intentos = 0;
        const intervalo = setInterval(() => {
            intentos++;
            const nombreInput = document.querySelector('#nombre');

            if (nombreInput) {
                console.log('✅ Formulario detectado');
                clearInterval(intervalo);
                setTimeout(crearBotones, 1000);
            } else if (intentos > 100) {
                console.log('❌ No se pudo detectar el formulario después de 100 intentos');
                clearInterval(intervalo);
            }
        }, 100);
    }

    // Ejecutar con múltiples métodos de inicio
    setTimeout(inicializar, 1000);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }

    window.addEventListener('load', inicializar);

    console.log('🤖 Script cargado completamente');
})();
