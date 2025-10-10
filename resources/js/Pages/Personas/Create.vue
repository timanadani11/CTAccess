<template>
  <Head title="Nueva Persona - CTAccess" />

  <div class="min-h-screen bg-theme-primary text-theme-primary overflow-y-auto">
    <!-- Toggle de tema fijo -->
    <div class="fixed top-4 right-4 z-50">
      <button @click="toggleTheme" class="p-2 rounded-lg bg-theme-card border border-theme-primary hover:bg-theme-secondary transition-all duration-200 shadow-theme-sm">
        <Icon :name="isDark ? 'sun' : 'moon'" :size="20" class="text-theme-secondary" />
      </button>
    </div>

    <!-- Container principal centrado -->
    <div class="min-h-screen flex flex-col items-center justify-start py-6 sm:py-8 lg:py-10 px-4 sm:px-6 lg:px-8 xl:px-12">

      <!-- Header con Logo -->
      <div class="w-full max-w-md sm:max-w-2xl lg:max-w-4xl xl:max-w-5xl 2xl:max-w-6xl mb-4 sm:mb-6">
        <div class="text-center">
          <div class="mx-auto h-14 w-14 sm:h-16 sm:w-16 rounded-2xl flex items-center justify-center mb-3 shadow-theme-lg" style="background: linear-gradient(135deg, #39A900, #50E5F9);">
            <Icon name="user-plus" :size="28" class="text-white sm:hidden" />
            <Icon name="user-plus" :size="32" class="text-white hidden sm:block" />
          </div>
          <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-theme-primary mb-1">Nueva Persona</h1>
          <p class="text-theme-secondary text-xs sm:text-sm lg:text-base">{{ getStepDescription() }}</p>
        </div>
      </div>

      <!-- Indicador de progreso horizontal superior -->
      <div class="w-full max-w-md sm:max-w-2xl lg:max-w-4xl xl:max-w-5xl 2xl:max-w-6xl mb-4 sm:mb-6">
        <div class="bg-theme-card backdrop-blur-lg rounded-2xl shadow-theme-lg p-4 sm:p-6 border border-theme-primary">
          <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div v-for="(step, index) in totalSteps" :key="step" class="flex items-center flex-1">
              <!-- Círculo del paso -->
              <div class="flex flex-col items-center flex-1">
                <div class="relative">
                  <div class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 rounded-full flex items-center justify-center text-sm sm:text-base font-bold transition-all duration-300 transform" :class="step <= currentStep ? 'bg-gradient-to-br from-green-400 to-green-600 text-white scale-110 shadow-lg' : 'bg-theme-secondary text-theme-muted border-2 border-theme-primary'">
                    <Icon v-if="step < currentStep" name="check" :size="20" class="sm:hidden" />
                    <Icon v-if="step < currentStep" name="check" :size="24" class="hidden sm:block" />
                    <span v-else>{{ step }}</span>
                  </div>
                  <!-- Anillo de progreso para el paso actual -->
                  <div v-if="step === currentStep" class="absolute -inset-1 bg-gradient-to-br from-green-400 to-green-600 rounded-full opacity-20 animate-pulse"></div>
                </div>
                <!-- Título del paso -->
                <div class="mt-2 text-center hidden sm:block">
                  <div class="text-xs lg:text-sm font-medium text-theme-primary truncate max-w-[100px] lg:max-w-[140px]" :class="step === currentStep ? 'text-green-600 font-bold' : ''">
                    {{ getStepTitle(step) }}
                  </div>
                  <div class="text-xs text-theme-muted truncate max-w-[100px] lg:max-w-[140px] hidden lg:block">{{ getStepSubtitle(step) }}</div>
                </div>
              </div>

              <!-- Línea conectora -->
              <div v-if="index < totalSteps - 1" class="flex-1 h-1 mx-2 sm:mx-3 rounded-full transition-all duration-300 relative -top-4 sm:-top-5 lg:-top-6" :class="step < currentStep ? 'bg-gradient-to-r from-green-500 to-green-400' : 'bg-theme-secondary'">
                <div v-if="step === currentStep" class="h-full bg-gradient-to-r from-green-500 to-green-400 rounded-full animate-pulse"></div>
              </div>
            </div>
          </div>
          <!-- Contador de pasos para móviles -->
          <div class="text-center text-sm text-theme-muted mt-2 sm:hidden">Paso {{ currentStep }} de {{ totalSteps }}</div>
        </div>
      </div>

      <!-- Formulario -->
      <div class="w-full max-w-md sm:max-w-2xl lg:max-w-4xl xl:max-w-5xl 2xl:max-w-6xl">

      <!-- Formulario paso a paso -->
      <div class="bg-theme-card backdrop-blur-lg rounded-2xl shadow-theme-lg p-5 sm:p-6 lg:p-8 xl:p-10 border border-theme-primary">

        <!-- Paso 1: Información Personal -->
        <div v-if="currentStep === 1" class="space-y-4 sm:space-y-5">

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 lg:gap-6">
            <div class="lg:col-span-2">
              <label for="nombre" class="block text-sm lg:text-base font-medium text-theme-primary mb-2">Nombre Completo *</label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none transition-colors">
                  <Icon name="user" :size="20" class="text-theme-muted group-focus-within:text-green-500" />
                </div>
                <input id="nombre" v-model="form.nombre" type="text" required class="block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 text-base border border-theme-primary rounded-xl bg-theme-secondary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 hover:border-green-300" placeholder="Ej: Juan Pérez García" />
              </div>
              <div v-if="form.errors.nombre" class="mt-2 text-sm text-red-500">{{ form.errors.nombre }}</div>
            </div>

            <div>
              <label for="documento" class="block text-sm lg:text-base font-medium text-theme-primary mb-2">Documento de Identidad</label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none transition-colors">
                  <Icon name="credit-card" :size="20" class="text-theme-muted group-focus-within:text-green-500" />
                </div>
                <input id="documento" v-model="form.documento" type="text" class="block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 text-base border border-theme-primary rounded-xl bg-theme-secondary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 hover:border-green-300" placeholder="Ej: 12345678" />
              </div>
            </div>

            <div>
              <label for="tipoPersona" class="block text-sm lg:text-base font-medium text-theme-primary mb-2">Tipo de Persona *</label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none transition-colors">
                  <Icon name="users" :size="20" class="text-theme-muted group-focus-within:text-green-500" />
                </div>
                <select id="tipoPersona" v-model="form.tipoPersona" required class="block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 text-base border border-theme-primary rounded-xl bg-theme-secondary text-theme-primary focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 hover:border-green-300 appearance-none cursor-pointer">
                  <option value="">Seleccionar tipo</option>
                  <option value="Aprendiz">Aprendiz</option>
                  <option value="Instructor">Instructor</option>
                  <option value="Visitante">Visitante</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center pointer-events-none">
                  <Icon name="chevron-down" :size="16" class="text-theme-muted" />
                </div>
              </div>
            </div>

            <div class="lg:col-span-2">
              <label for="correo" class="block text-sm lg:text-base font-medium text-theme-primary mb-2">Correo Electrónico</label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none transition-colors">
                  <Icon name="mail" :size="20" class="text-theme-muted group-focus-within:text-green-500" />
                </div>
                <input id="correo" v-model="form.correo" type="email" class="block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 text-base border border-theme-primary rounded-xl bg-theme-secondary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 hover:border-green-300" placeholder="correo@ejemplo.com" />
              </div>
              <p class="mt-2 text-xs sm:text-sm text-theme-muted flex items-center">
                <Icon name="info" :size="14" class="mr-1" />Se enviará un QR por correo si se proporciona
              </p>
            </div>
          </div>
        </div>

        <!-- Paso 2: Portátiles -->
        <div v-if="currentStep === 2" class="space-y-4 sm:space-y-5">

          <div v-if="form.portatiles.length === 0" class="text-center py-12 lg:py-16">
            <div class="inline-flex items-center justify-center w-20 h-20 lg:w-24 lg:h-24 rounded-full mb-6" style="background: linear-gradient(135deg, #50E5F9, #00B4D8); opacity: 0.1;">
              <Icon name="laptop" :size="48" class="text-blue-500" style="opacity: 1;" />
            </div>
            <Icon name="laptop" :size="64" class="mx-auto text-theme-muted mb-4 lg:hidden" />
            <h3 class="text-lg lg:text-xl font-semibold text-theme-primary mb-2">Sin portátiles registrados</h3>
            <p class="text-theme-muted mb-6 max-w-md mx-auto text-sm lg:text-base">Agrega los portátiles que esta persona utilizará. Puedes omitir este paso si no aplica.</p>
            <button type="button" @click="addPortatil" class="inline-flex items-center px-6 py-3 rounded-xl text-white font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105" style="background: linear-gradient(135deg, #50E5F9, #00B4D8);">
              <Icon name="plus" :size="18" class="mr-2" />Agregar Primer Portátil
            </button>
          </div>

          <div v-else class="space-y-4 sm:space-y-5">
            <div v-for="(portatil, index) in form.portatiles" :key="`portatil-${index}`" class="border-2 border-theme-primary rounded-xl p-5 lg:p-6 bg-theme-secondary hover:border-blue-400 transition-all duration-200">
              <div class="flex items-center justify-between mb-5">
                <h4 class="text-base lg:text-lg font-semibold text-theme-primary flex items-center">
                  <span class="inline-flex items-center justify-center w-8 h-8 lg:w-10 lg:h-10 text-white text-sm lg:text-base font-bold rounded-full mr-3 shadow-md" style="background: linear-gradient(135deg, #50E5F9, #00B4D8);">{{ index + 1 }}</span>
                  Portátil {{ index + 1 }}
                </h4>
                <button type="button" @click="removePortatil(index)" class="text-red-500 hover:text-red-700 transition-all duration-200 p-2 hover:bg-red-50 rounded-lg">
                  <Icon name="trash" :size="18" />
                </button>
              </div>
              <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-3">
                  <label class="block text-sm lg:text-base font-medium text-theme-primary mb-2">Serial *</label>
                  <div class="relative">
                    <input v-model="portatil.serial" type="text" required class="w-full px-4 py-3 lg:py-4 text-base border border-theme-primary rounded-xl bg-theme-primary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-300" placeholder="Ej: ABC123456DEF" />
                  </div>
                </div>
                <div class="lg:col-span-1">
                  <label class="block text-sm lg:text-base font-medium text-theme-primary mb-2">Marca *</label>
                  <input v-model="portatil.marca" type="text" required class="w-full px-4 py-3 lg:py-4 text-base border border-theme-primary rounded-xl bg-theme-primary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-300" placeholder="Dell, HP, Lenovo..." />
                </div>
                <div class="lg:col-span-2">
                  <label class="block text-sm lg:text-base font-medium text-theme-primary mb-2">Modelo *</label>
                  <input v-model="portatil.modelo" type="text" required class="w-full px-4 py-3 lg:py-4 text-base border border-theme-primary rounded-xl bg-theme-primary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-300" placeholder="Inspiron 15, ThinkPad X1..." />
                </div>
              </div>
            </div>
            <button type="button" @click="addPortatil" class="w-full py-4 border-2 border-dashed border-theme-secondary rounded-xl text-theme-secondary hover:border-blue-400 hover:text-blue-400 hover:bg-blue-50/5 transition-all duration-200 font-medium">
              <Icon name="plus" :size="18" class="mr-2" />Agregar Otro Portátil
            </button>
          </div>
        </div>

        <!-- Paso 3: Vehículos -->
        <div v-if="currentStep === 3" class="space-y-4 sm:space-y-5">

          <div v-if="form.vehiculos.length === 0" class="text-center py-12 lg:py-16">
            <div class="inline-flex items-center justify-center w-20 h-20 lg:w-24 lg:h-24 rounded-full mb-6" style="background: linear-gradient(135deg, #FDC300, #E6B000); opacity: 0.1;">
              <Icon name="car" :size="48" class="text-yellow-500" style="opacity: 1;" />
            </div>
            <Icon name="car" :size="64" class="mx-auto text-theme-muted mb-4 lg:hidden" />
            <h3 class="text-lg lg:text-xl font-semibold text-theme-primary mb-2">Sin vehículos registrados</h3>
            <p class="text-theme-muted mb-6 max-w-md mx-auto text-sm lg:text-base">Registra los vehículos que esta persona utiliza para acceder al centro. Puedes omitir este paso si no aplica.</p>
            <button type="button" @click="addVehiculo" class="inline-flex items-center px-6 py-3 rounded-xl text-black font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105" style="background: linear-gradient(135deg, #FDC300, #E6B000);">
              <Icon name="plus" :size="18" class="mr-2" />Agregar Primer Vehículo
            </button>
          </div>

          <div v-else class="space-y-4 sm:space-y-5">
            <div v-for="(vehiculo, index) in form.vehiculos" :key="`vehiculo-${index}`" class="border-2 border-theme-primary rounded-xl p-5 lg:p-6 bg-theme-secondary hover:border-yellow-400 transition-all duration-200">
              <div class="flex items-center justify-between mb-5">
                <h4 class="text-base lg:text-lg font-semibold text-theme-primary flex items-center">
                  <span class="inline-flex items-center justify-center w-8 h-8 lg:w-10 lg:h-10 text-black text-sm lg:text-base font-bold rounded-full mr-3 shadow-md" style="background: linear-gradient(135deg, #FDC300, #E6B000);">{{ index + 1 }}</span>
                  Vehículo {{ index + 1 }}
                </h4>
                <button type="button" @click="removeVehiculo(index)" class="text-red-500 hover:text-red-700 transition-all duration-200 p-2 hover:bg-red-50 rounded-lg">
                  <Icon name="trash" :size="18" />
                </button>
              </div>
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="lg:col-span-2">
                  <label class="block text-sm lg:text-base font-medium text-theme-primary mb-2">Tipo de Vehículo *</label>
                  <div class="relative">
                    <select v-model="vehiculo.tipo" required class="w-full px-4 py-3 lg:py-4 text-base border border-theme-primary rounded-xl bg-theme-primary text-theme-primary focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 hover:border-yellow-300 appearance-none cursor-pointer">
                      <option value="">Seleccionar tipo</option>
                      <option value="Automóvil">🚗 Automóvil</option>
                      <option value="Motocicleta">🏍️ Motocicleta</option>
                      <option value="Bicicleta">🚲 Bicicleta</option>
                      <option value="Camioneta">🚙 Camioneta</option>
                      <option value="Camión">🚛 Camión</option>
                      <option value="Otro">🚐 Otro</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center pointer-events-none">
                      <Icon name="chevron-down" :size="16" class="text-theme-muted" />
                    </div>
                  </div>
                </div>
                <div class="lg:col-span-2">
                  <label class="block text-sm lg:text-base font-medium text-theme-primary mb-2">Placa *</label>
                  <input v-model="vehiculo.placa" type="text" required class="w-full px-4 py-3 lg:py-4 text-base border border-theme-primary rounded-xl bg-theme-primary text-theme-primary placeholder-theme-muted focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 uppercase hover:border-yellow-300 font-mono tracking-wider text-lg" placeholder="ABC-123" @input="vehiculo.placa = vehiculo.placa.toUpperCase()" />
                </div>
              </div>
            </div>
            <button type="button" @click="addVehiculo" class="w-full py-4 border-2 border-dashed border-theme-secondary rounded-xl text-theme-secondary hover:border-yellow-400 hover:text-yellow-400 hover:bg-yellow-50/5 transition-all duration-200 font-medium">
              <Icon name="plus" :size="18" class="mr-2" />Agregar Otro Vehículo
            </button>
          </div>
        </div>

        <!-- Paso 4: Resumen -->
        <div v-if="currentStep === 4" class="space-y-4 sm:space-y-5">

          <!-- Header del resumen -->
          <div class="text-center mb-6 lg:mb-8">
            <h3 class="text-xl lg:text-2xl font-bold text-theme-primary mb-2">Revisa tu información</h3>
            <p class="text-theme-muted text-sm lg:text-base">Verifica que todos los datos sean correctos antes de crear el registro</p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 lg:gap-6">
            <!-- Información Personal -->
            <div class="border-2 border-theme-primary rounded-xl p-5 lg:p-6 bg-gradient-to-br from-green-50/5 to-theme-secondary hover:border-green-400 transition-all duration-200 lg:col-span-2">
              <h4 class="text-lg font-semibold text-theme-primary mb-4 flex items-center">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #39A900, #2d7a00);">
                  <Icon name="user" :size="18" class="text-white" />
                </div>
                Información Personal
              </h4>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-theme-primary rounded-lg p-4">
                  <div class="text-xs text-theme-muted mb-1">Nombre Completo</div>
                  <div class="text-base font-semibold text-theme-primary">{{ form.nombre || 'No especificado' }}</div>
                </div>
                <div class="bg-theme-primary rounded-lg p-4">
                  <div class="text-xs text-theme-muted mb-1">Documento</div>
                  <div class="text-base font-semibold text-theme-primary">{{ form.documento || 'No especificado' }}</div>
                </div>
                <div class="bg-theme-primary rounded-lg p-4">
                  <div class="text-xs text-theme-muted mb-1">Tipo de Persona</div>
                  <div class="text-base font-semibold text-theme-primary">{{ form.tipoPersona || 'No especificado' }}</div>
                </div>
                <div class="bg-theme-primary rounded-lg p-4">
                  <div class="text-xs text-theme-muted mb-1">Correo Electrónico</div>
                  <div class="text-base font-semibold text-theme-primary truncate">{{ form.correo || 'No especificado' }}</div>
                </div>
              </div>
            </div>

            <!-- Portátiles -->
            <div v-if="form.portatiles.length > 0" class="border-2 border-theme-primary rounded-xl p-5 lg:p-6 bg-gradient-to-br from-blue-50/5 to-theme-secondary hover:border-blue-400 transition-all duration-200" :class="form.vehiculos.length === 0 ? 'lg:col-span-2' : ''">
              <h4 class="text-lg font-semibold text-theme-primary mb-4 flex items-center">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #50E5F9, #00B4D8);">
                  <Icon name="laptop" :size="18" class="text-white" />
                </div>
                Portátiles <span class="ml-2 text-sm font-normal text-theme-muted">({{ form.portatiles.length }})</span>
              </h4>
              <div class="space-y-3">
                <div v-for="(portatil, index) in form.portatiles" :key="index" class="bg-theme-primary rounded-lg p-4">
                  <div class="flex items-start justify-between mb-2">
                    <div class="text-xs text-theme-muted">Portátil {{ index + 1 }}</div>
                    <div class="inline-flex items-center justify-center w-6 h-6 text-white text-xs font-bold rounded-full" style="background: #50E5F9;">{{ index + 1 }}</div>
                  </div>
                  <div class="text-sm font-semibold text-theme-primary mb-1">{{ portatil.marca }} {{ portatil.modelo }}</div>
                  <div class="text-xs text-theme-muted">Serial: <span class="font-mono text-theme-primary">{{ portatil.serial }}</span></div>
                </div>
              </div>
            </div>

            <!-- Vehículos -->
            <div v-if="form.vehiculos.length > 0" class="border-2 border-theme-primary rounded-xl p-5 lg:p-6 bg-gradient-to-br from-yellow-50/5 to-theme-secondary hover:border-yellow-400 transition-all duration-200" :class="form.portatiles.length === 0 ? 'lg:col-span-2' : ''">
              <h4 class="text-lg font-semibold text-theme-primary mb-4 flex items-center">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #FDC300, #E6B000);">
                  <Icon name="car" :size="18" class="text-black" />
                </div>
                Vehículos <span class="ml-2 text-sm font-normal text-theme-muted">({{ form.vehiculos.length }})</span>
              </h4>
              <div class="space-y-3">
                <div v-for="(vehiculo, index) in form.vehiculos" :key="index" class="bg-theme-primary rounded-lg p-4">
                  <div class="flex items-start justify-between mb-2">
                    <div class="text-xs text-theme-muted">Vehículo {{ index + 1 }}</div>
                    <div class="inline-flex items-center justify-center w-6 h-6 text-black text-xs font-bold rounded-full" style="background: #FDC300;">{{ index + 1 }}</div>
                  </div>
                  <div class="text-sm font-semibold text-theme-primary mb-1">{{ vehiculo.tipo }}</div>
                  <div class="text-xs text-theme-muted">Placa: <span class="font-mono text-theme-primary font-bold text-base">{{ vehiculo.placa }}</span></div>
                </div>
              </div>
            </div>

            <!-- Mensaje si no hay portátiles ni vehículos -->
            <div v-if="form.portatiles.length === 0 && form.vehiculos.length === 0" class="lg:col-span-2 text-center py-6 border-2 border-dashed border-theme-secondary rounded-xl">
              <Icon name="info" :size="32" class="mx-auto text-theme-muted mb-2" />
              <p class="text-theme-muted text-sm">No se registraron portátiles ni vehículos para esta persona</p>
            </div>
          </div>
        </div>

        <!-- Botones de navegación -->
        <div class="flex flex-col sm:flex-row justify-between gap-3 sm:gap-4 pt-8 mt-6 border-t border-theme-primary">
          <button v-if="currentStep > 1" type="button" @click="previousStep" class="inline-flex items-center justify-center px-5 py-3 lg:px-6 lg:py-3.5 border-2 border-theme-primary rounded-xl text-theme-secondary hover:bg-theme-secondary hover:border-green-400 transition-all duration-200 order-2 sm:order-1 font-medium text-base">
            <Icon name="arrow-left" :size="18" class="mr-2" />Anterior
          </button>
          <div v-else class="hidden sm:block"></div>

          <div class="flex flex-col sm:flex-row gap-3 order-1 sm:order-2">
            <Link :href="route('personas.index')" class="inline-flex items-center justify-center px-5 py-3 lg:px-6 lg:py-3.5 border-2 border-red-400 rounded-xl text-red-600 hover:bg-red-50/10 hover:border-red-500 transition-all duration-200 font-medium text-base">
              <Icon name="x" :size="18" class="mr-2" />Cancelar
            </Link>

            <button v-if="currentStep < totalSteps" type="button" @click="nextStep" :disabled="!canProceedToNextStep()" class="inline-flex items-center justify-center px-6 py-3 lg:px-8 lg:py-3.5 rounded-xl text-white font-semibold text-base transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none transform hover:scale-105 disabled:hover:scale-100" style="background: linear-gradient(135deg, #39A900, #2d7a00);">
              Siguiente<Icon name="arrow-right" :size="18" class="ml-2" />
            </button>

            <button v-else type="submit" @click="submit" :disabled="form.processing" class="inline-flex items-center justify-center px-6 py-3 lg:px-8 lg:py-3.5 rounded-xl text-white font-semibold text-base transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none transform hover:scale-105 disabled:hover:scale-100" style="background: linear-gradient(135deg, #39A900, #2d7a00);">
              <Icon v-if="form.processing" name="loader" :size="18" class="animate-spin mr-2" />
              <span v-if="form.processing">Creando...</span><span v-else><Icon name="check" :size="18" class="mr-2" />Crear Persona</span>
            </button>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Icon from '@/Components/Icon.vue'
import { useTheme } from '@/composables/useTheme'
import { ref } from 'vue'

defineOptions({ layout: null })

const { isDark, toggleTheme } = useTheme()
const currentStep = ref(1)
const totalSteps = 4

const form = useForm({
  documento: '', nombre: '', tipoPersona: '', correo: '', portatiles: [], vehiculos: []
})

const getStepDescription = () => {
  const descriptions = {
    1: 'Completa la información personal básica',
    2: 'Agrega portátiles asociados (opcional)',
    3: 'Registra vehículos (opcional)',
    4: 'Revisa y confirma la información'
  }
  return descriptions[currentStep.value]
}

const getStepTitle = (step) => {
  const titles = {
    1: 'Información Personal',
    2: 'Portátiles',
    3: 'Vehículos', 
    4: 'Resumen'
  }
  return titles[step]
}

const getStepSubtitle = (step) => {
  const subtitles = {
    1: 'Datos básicos',
    2: 'Equipos (opcional)',
    3: 'Transporte (opcional)',
    4: 'Confirmar datos'
  }
  return subtitles[step]
}

const nextStep = () => {
  if (currentStep.value < totalSteps && canProceedToNextStep()) {
    currentStep.value++
  }
}

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

const canProceedToNextStep = () => {
  switch (currentStep.value) {
    case 1: return form.nombre.trim() && form.tipoPersona
    case 2:
    case 3: return true
    default: return true
  }
}

const addPortatil = () => {
  form.portatiles.push({ serial: '', marca: '', modelo: '' })
}

const removePortatil = (index) => {
  form.portatiles.splice(index, 1)
}

const addVehiculo = () => {
  form.vehiculos.push({ tipo: '', placa: '' })
}

const removeVehiculo = (index) => {
  form.vehiculos.splice(index, 1)
}

const submit = () => {
  if (!form.nombre.trim()) {
    alert('El nombre es obligatorio');
    return;
  }
  
  if (!form.tipoPersona) {
    alert('Debe seleccionar un tipo de persona');
    return;
  }

  form.post(route('personas.store'), {
    onSuccess: (response) => {
      console.log('Persona creada exitosamente:', response);
    },
    onError: (errors) => {
      console.error('Errores de validación:', errors);
      if (errors.message && (errors.message.includes('CSRF') || errors.message.includes('expired'))) {
        window.location.reload();
        return;
      }
      const firstError = Object.values(errors)[0];
      if (firstError && typeof firstError === 'string') {
        alert(`Error: ${firstError}`);
      }
    },
    preserveScroll: true,
    preserveState: false,
  })
}
</script>
