<template>
  <SidebarLayout>
    <Head title="Book Appointment">
      <meta name="description" content="Book your next haircut or grooming appointment at Mickyes Coiffure, Newcastle's top barbershop." />
      <meta property="og:title" content="Book Appointment - Mickyes Coiffure" />
      <meta property="og:description" content="Easily book your next barber appointment online at Mickyes Coiffure." />
      <meta property="og:type" content="website" />
      <meta property="og:url" content="https://mickyes.com/booking/create" />
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" content="Book Appointment - Mickyes Coiffure" />
      <meta name="twitter:description" content="Book your next haircut or grooming session online at Mickyes Coiffure." />
    </Head>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <!-- Hero Section -->
      <div class="bg-gradient-to-r from-gray-400 to-gray-400 text-white w-full max-w-full px-2 py-6 sm:py-8 lg:py-12">
        <div class="max-w-4xl mx-auto w-full max-w-full px-0 sm:px-4 lg:px-8 text-center">
          <h1 class="text-lg sm:text-2xl lg:text-4xl font-bold mb-1 sm:mb-2 break-words">Book Your Perfect Cut</h1>
          <p class="text-gray-100 text-base sm:text-lg break-words">Choose your service, date, and preferred barber</p>
        </div>
      </div>

      <main class="py-4 sm:py-6 lg:py-10">
        <div class="max-w-4xl mx-auto px-2 sm:px-4 lg:px-8 w-full">
          <!-- Progress Indicator -->
          <div class="mb-6 sm:mb-8">
            <!-- Mobile: Only show current step -->
            <div class="block sm:hidden mb-4">
              <div class="flex flex-col items-center justify-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-base font-semibold bg-green-500 text-white mb-2">{{ currentStep }}</div>
                <div class="text-sm font-medium text-gray-700 mb-1">{{ currentStepLabel }}</div>
                <div class="w-full h-1 bg-gray-200 rounded">
                  <div class="h-full rounded bg-green-500 transition-all duration-300" :style="{ width: stepProgress + '%' }"></div>
                </div>
              </div>
            </div>
            <!-- Desktop: Full stepper -->
            <div class="hidden sm:block overflow-x-auto">
              <div class="flex items-center justify-between min-w-[500px] sm:min-w-0">
                <div class="flex items-center">
                  <div :class="[
                    'w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold',
                    form.service ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600'
                  ]">1</div>
                  <span class="ml-1 sm:ml-2 text-xs sm:text-sm font-medium text-gray-700">Service</span>
                </div>
                <div class="flex-1 h-1 mx-2 sm:mx-4 bg-gray-200 rounded">
                  <div :class="[
                    'h-full rounded transition-all duration-300',
                    form.service ? 'bg-green-500' : 'bg-gray-200'
                  ]" :style="{ width: form.service ? '25%' : '0%' }"></div>
                </div>
                <div class="flex items-center">
                  <div :class="[
                    'w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold',
                    form.date ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600'
                  ]">2</div>
                  <span class="ml-1 sm:ml-2 text-xs sm:text-sm font-medium text-gray-700">Date & Time</span>
                </div>
                <div class="flex-1 h-1 mx-2 sm:mx-4 bg-gray-200 rounded">
                  <div :class="[
                    'h-full rounded transition-all duration-300',
                    form.date && form.time ? 'bg-green-500' : 'bg-gray-200'
                  ]" :style="{ width: form.date && form.time ? '50%' : '0%' }"></div>
                </div>
                <div class="flex items-center">
                  <div :class="[
                    'w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold',
                    form.barber_id ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600'
                  ]">3</div>
                  <span class="ml-1 sm:ml-2 text-xs sm:text-sm font-medium text-gray-700">Barber</span>
                </div>
                <div class="flex-1 h-1 mx-2 sm:mx-4 bg-gray-200 rounded">
                  <div :class="[
                    'h-full rounded transition-all duration-300',
                    form.barber_id ? 'bg-green-500' : 'bg-gray-200'
                  ]" :style="{ width: form.barber_id ? '100%' : '0%' }"></div>
                </div>
                <div class="flex items-center">
                  <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold bg-gray-300 text-gray-600">4</div>
                  <span class="ml-1 sm:ml-2 text-xs sm:text-sm font-medium text-gray-700">Confirm</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Form Card -->
          <div class="bg-white rounded-2xl shadow-xl overflow-hidden w-full max-w-md mx-auto">
            <div class="p-3 sm:p-6 lg:p-8">
              <form @submit.prevent="submitForm" class="space-y-6 sm:space-y-8">
                <!-- Admin/Barber Only Sections -->
                <div v-if="user.role === 'admin' || user.role === 'barber'" class="space-y-4 sm:space-y-6">
                  <!-- User Selection Card -->
                  <div class="bg-blue-50 rounded-xl p-3 sm:p-6 border border-blue-200">
                    <h3 class="text-base sm:text-lg font-semibold text-blue-900 mb-2 sm:mb-4 flex items-center">
                      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      Select Customer
                    </h3>
                    <div v-if="loadingUsers" class="flex items-center text-blue-600">
                      <svg class="animate-spin -ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Loading users...
                    </div>
                    <div v-else-if="userError" class="text-red-600 bg-red-100 p-3 rounded-lg">{{ userError }}</div>
                    <!-- Minimal Custom Dropdown for Customer Selection -->
                    <div v-else class="relative" ref="customerDropdownRef">
                      <button
                        type="button"
                        @click="toggleCustomerDropdown"
                        class="w-full bg-white border-2 rounded-xl px-4 py-3 min-h-[44px] text-left shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 text-base transition-all duration-200"
                        :class="form.user_id ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'"
                      >
                        <span v-if="!form.user_id" class="text-gray-500">Select a customer</span>
                        <span v-else class="text-gray-900 font-medium">{{ getSelectedCustomerName() }}</span>
                        <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                      </button>
                      <div v-if="showCustomerDropdown" class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl max-h-64 overflow-auto">
                        <button
                          type="button"
                          v-for="user in availableUsers"
                          :key="user.id"
                          @click="selectCustomer(user)"
                          class="w-full text-left px-4 py-3 text-base font-medium text-gray-900 hover:bg-blue-50 hover:text-blue-900 border-b border-gray-100 last:border-b-0 transition-colors duration-150"
                        >
                          <span class="block">{{ user.name }}</span>
                          <span class="text-sm text-blue-600 font-semibold">{{ user.email }}</span>
                        </button>
                      </div>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Selected: {{ form.user_id }}</div>
                  </div>

                  <!-- Skip Payment Option -->
                  <div class="bg-yellow-50 rounded-xl p-3 sm:p-6 border border-yellow-200">
                    <label class="flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        id="skip_payment"
                        v-model="form.skip_payment"
                        class="h-5 w-5 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded"
                      />
                      <div class="ml-3">
                        <span class="text-yellow-900 font-medium">Skip payment</span>
                        <p class="text-yellow-700 text-xs sm:text-sm">Book directly without payment processing</p>
                      </div>
                    </label>
                  </div>
                </div>

                <!-- Service Selection -->
                <div class="space-y-2 sm:space-y-4">
                  <h3 class="text-base sm:text-xl font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Choose Your Service
                  </h3>

                  <!-- Mobile Service Selection -->
                  <div class="block md:hidden">
                    <div class="relative" ref="serviceDropdownRef">
                      <button
                        type="button"
                        @click="toggleServiceDropdown"
                        :class="[
                          'w-full bg-white border-2 rounded-xl px-4 py-3 min-h-[44px] text-left shadow-sm focus:ring-2 focus:ring-green-500 focus:ring-opacity-20 text-base transition-all duration-200',
                          form.service ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-gray-400'
                        ]"
                      >
                        <span v-if="!form.service" class="text-gray-500">Choose a service...</span>
                        <span v-else class="text-gray-900 font-medium">{{ getSelectedServiceName() }}</span>
                        <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                      </button>

                      <div v-if="showServiceDropdown" class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl max-h-64 overflow-auto">
                        <button
                          type="button"
                          v-for="service in props.services"
                          :key="service.id"
                          @click="selectService(service)"
                          class="w-full text-left px-4 py-3 text-base font-medium text-gray-900 hover:bg-green-50 hover:text-green-900 border-b border-gray-100 last:border-b-0 transition-colors duration-150"
                        >
                          <span class="block">{{ service.name }}</span>
                          <span class="text-sm text-green-600 font-semibold">£{{ service.price }}</span>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Desktop Service Selection -->
                  <select
                    id="service"
                    v-model="form.service"
                    :class="[
                      'mt-1 hidden md:block w-full rounded-xl border-2 shadow-sm focus:ring-2 focus:ring-green-500 focus:ring-opacity-20 text-base p-3 sm:p-4 min-h-[44px] transition-all duration-200',
                      form.service ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-gray-400'
                    ]"
                    required
                  >
                    <option value="">Choose a service...</option>
                    <option v-for="service in props.services" :key="service.id" :value="service.slug">
                      {{ service.name }} - £{{ service.price }}
                    </option>
                  </select>
                </div>

                <!-- Date & Time Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                  <!-- Date Selection -->
                  <div class="space-y-2 sm:space-y-4">
                    <h3 class="text-base sm:text-xl font-semibold text-gray-900 flex items-center">
                      <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      Select Date
                    </h3>
                    <div class="relative" ref="calendarRef">
                      <button
                        type="button"
                        @click="toggleCalendar"
                        :class="[
                          'w-full rounded-xl border-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 bg-white text-left px-4 py-3 min-h-[44px] text-base transition-all duration-200',
                          form.date ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'
                        ]"
                      >
                        <span v-if="!form.date" class="text-gray-500">Select a date...</span>
                        <span v-else class="text-gray-900 font-medium">{{ form.date }}</span>
                        <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                      </button>

                      <div v-if="showCalendar" class="absolute z-20 bg-white border border-gray-200 rounded-xl shadow-xl mt-2 p-4 w-full max-w-sm">
                        <div class="flex items-center justify-between mb-4">
                          <button type="button" @click="prevMonth" class="p-2 hover:bg-gray-100 rounded-lg text-base font-medium">‹</button>
                          <span class="font-semibold text-base text-gray-900">{{ monthNames[currentMonth] }} {{ currentYear }}</span>
                          <button type="button" @click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg text-base font-medium">›</button>
                        </div>
                        <div class="grid grid-cols-7 gap-1 mb-2">
                          <div v-for="day in weekDays" :key="day" class="text-center text-xs font-medium text-gray-600 py-2">{{ day }}</div>
                        </div>
                        <div class="grid grid-cols-7 gap-1">
                          <div v-for="(date, idx) in calendarDays" :key="idx" class="aspect-square">
                            <button
                              v-if="date"
                              type="button"
                              :disabled="isPastDate(date)"
                              @click="selectDate(date)"
                              :class="[
                                'w-full h-full flex items-center justify-center rounded-lg text-sm font-medium transition-all duration-150',
                                isSelectedDate(date)
                                  ? 'bg-blue-600 text-white shadow-md'
                                  : isPastDate(date)
                                    ? 'text-gray-400 cursor-not-allowed'
                                    : 'hover:bg-blue-100 hover:text-blue-900 text-gray-700'
                              ]"
                            >
                              {{ date.getDate() }}
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Time Selection -->
                  <div class="space-y-2 sm:space-y-4">
                    <h3 class="text-base sm:text-xl font-semibold text-gray-900 flex items-center">
                      <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      Select Time
                    </h3>

                    <!-- Mobile Time Selection -->
                    <div class="block md:hidden">
                      <div class="relative" ref="timeDropdownRef">
                        <button
                          type="button"
                          @click="toggleTimeDropdown"
                          :class="[
                            'w-full bg-white border-2 rounded-xl px-4 py-3 min-h-[44px] text-left shadow-sm focus:ring-2 focus:ring-purple-500 focus:ring-opacity-20 text-base transition-all duration-200',
                            form.time ? 'border-purple-500 bg-purple-50' : 'border-gray-300 hover:border-gray-400'
                          ]"
                        >
                          <span v-if="!form.time" class="text-gray-500">Choose a time...</span>
                          <span v-else class="text-gray-900 font-medium">{{ form.time }}</span>
                          <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                          </svg>
                        </button>

                        <div v-if="showTimeDropdown" class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl max-h-64 overflow-auto">
                          <button
                            type="button"
                            v-for="time in availableTimes"
                            :key="time"
                            @click="selectTime(time)"
                            class="w-full text-left px-4 py-3 text-base font-medium text-gray-900 hover:bg-purple-50 hover:text-purple-900 border-b border-gray-100 last:border-b-0 transition-colors duration-150"
                          >
                            {{ time }}
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Desktop Time Selection -->
                    <select
                      id="time"
                      v-model="form.time"
                      :class="[
                        'mt-1 hidden md:block w-full rounded-xl border-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:ring-opacity-20 text-base p-3 sm:p-4 min-h-[44px] transition-all duration-200',
                        form.time ? 'border-purple-500 bg-purple-50' : 'border-gray-300 hover:border-gray-400'
                      ]"
                      required
                    >
                      <option value="">Choose a time...</option>
                      <option v-for="time in availableTimes" :key="time" :value="time">
                        {{ time }}
                      </option>
                    </select>
                  </div>
                </div>

                <!-- Barber Selection -->
                <div v-if="form.date && form.time" class="space-y-4">
                  <h3 class="text-base sm:text-xl font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Choose Your Barber
                  </h3>

                  <div v-if="loadingBarbers" class="flex items-center justify-center py-8 text-gray-600">
                    <svg class="animate-spin -ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading available barbers...
                  </div>

                  <div v-else-if="barberError" class="text-red-600 bg-red-100 p-4 rounded-xl border border-red-200">{{ barberError }}</div>

                  <div v-else-if="availableBarbers.length" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label v-for="barber in availableBarbers" :key="barber.id"
                      :class="[
                        'relative flex items-center p-6 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-md',
                        form.barber_id === barber.id
                          ? 'border-orange-500 bg-orange-50 shadow-lg ring-2 ring-orange-500 ring-opacity-20'
                          : 'border-gray-300 bg-white hover:border-orange-300'
                      ]"
                    >
                      <input
                        type="radio"
                        v-model="form.barber_id"
                        :value="barber.id"
                        class="sr-only"
                        required
                      />

                      <!-- Selection Indicator -->
                      <div v-if="form.barber_id === barber.id" class="absolute top-3 right-3">
                        <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center">
                          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                          </svg>
                        </div>
                      </div>

                      <!-- Barber Photo -->
                      <div class="relative">
                        <img
                          v-if="barber.user && barber.user.profile_photo"
                          :src="`/storage/${barber.user.profile_photo}`"
                          alt="Barber photo"
                          :class="[
                            'w-16 h-16 rounded-full object-cover border-4 transition-all duration-200',
                            form.barber_id === barber.id ? 'border-orange-500' : 'border-gray-200'
                          ]"
                        />
                        <div v-else
                          :class="[
                            'w-16 h-16 rounded-full flex items-center justify-center font-bold text-xl transition-all duration-200',
                            form.barber_id === barber.id
                              ? 'bg-orange-500 text-white border-4 border-orange-600'
                              : 'bg-gray-200 text-gray-600 border-4 border-gray-300'
                          ]"
                        >
                          {{ (barber.user && barber.user.name ? barber.user.name.charAt(0) : 'B') }}
                        </div>
                      </div>

                      <!-- Barber Info -->
                      <div class="ml-4 flex-1">
                        <h4 class="text-lg font-semibold text-gray-900">
                          {{ barber.user && barber.user.name ? barber.user.name : ('Barber #' + barber.id) }}
                        </h4>
                        <p class="text-sm text-gray-600">Available at {{ form.time }}</p>
                        <div v-if="barber.experience" class="text-sm text-orange-600 font-medium mt-1">
                          {{ barber.experience }} years experience
                        </div>
                      </div>
                    </label>
                  </div>

                  <div v-else class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <p class="text-gray-500">No barbers available for this time slot.</p>
                    <p class="text-sm text-gray-400 mt-1">Please try a different time.</p>
                  </div>
                </div>

                <!-- Notes Section -->
                <div class="space-y-2 sm:space-y-4">
                  <h3 class="text-base sm:text-xl font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Additional Notes <span class="text-xs text-gray-400 ml-2">(Optional)</span>
                  </h3>
                  <textarea
                    v-model="form.notes"
                    class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-opacity-20 text-base p-3 sm:p-4 min-h-[44px]"
                    rows="3"
                    placeholder="Any specific requests, preferences, or special instructions?"
                  ></textarea>
                </div>

                <!-- Confirm Button (Sticky on mobile) -->
                <div class="block sm:hidden h-16"></div>
              </form>
            </div>
            <!-- Sticky Confirm Button for Mobile -->
            <div class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 px-2 py-3 sm:hidden">
              <button
                type="submit"
                class="w-full flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-xl font-semibold text-base shadow-lg hover:bg-green-700 transition-all duration-200"
                @click="submitForm"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Confirm Booking
              </button>
            </div>
            <!-- Desktop Confirm Button -->
            <div class="hidden sm:block mt-8 px-4 pb-4">
              <button
                type="submit"
                class="w-full flex items-center justify-center px-4 py-4 bg-green-600 text-white rounded-xl font-semibold text-lg shadow-lg hover:bg-green-700 transition-all duration-200"
                @click="submitForm"
              >
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Confirm Booking
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>
    <PaymentModal
      v-if="!form.skip_payment"
      :show="showPaymentModal"
      :booking="createdBooking"
      @close="showPaymentModal = false"
      @payment-success="handlePaymentSuccess"
    />
  </SidebarLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import PaymentModal from '@/Components/PaymentModal.vue'

const props = defineProps({
  user: {
    type: Object,
    required: true,
    default: () => ({ role: 'customer' })
  },
  services: {
    type: Array,
    default: () => []
  }
})

const form = ref({
  service: '',
  date: '',
  time: '',
  barber_id: '',
  notes: '',
  processing: false,
  user_id: '',
  skip_payment: false
})

const availableUsers = ref([])
const loadingUsers = ref(false)
const userError = ref('')

// Mobile dropdown states
const showServiceDropdown = ref(false)
const showTimeDropdown = ref(false)
const serviceDropdownRef = ref(null)
const timeDropdownRef = ref(null)
const calendarRef = ref(null)

// Load users if admin or barber
onMounted(async () => {
  if (props.user && (props.user.role === 'admin' || props.user.role === 'barber')) {
    loadingUsers.value = true
    try {
      const response = await fetch('/users/customers')
      const data = await response.json()
      availableUsers.value = data.users || []
    } catch (e) {
      userError.value = 'Failed to load users.'
    } finally {
      loadingUsers.value = false
    }
  }

  // Add click outside listener
  document.addEventListener('click', handleClickOutside)
})

// Get today's date for the minimum date
const today = new Date()
const minDate = computed(() => {
  return today.toISOString().split('T')[0]
})

// 1-hour time slots (9 AM to 6 PM)
const availableTimes = [
  '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
]

const availableBarbers = ref([])
const loadingBarbers = ref(false)
const barberError = ref('')

watch(() => [form.value.date, form.value.time], async ([date, time]) => {
  form.value.barber_id = ''
  availableBarbers.value = []
  barberError.value = ''
  if (date && time) {
    loadingBarbers.value = true
    try {
      const response = await fetch(`/api/available-barbers?date=${date}&time=${time}`)
      const data = await response.json()
      availableBarbers.value = data.barbers || []
    } catch (e) {
      barberError.value = 'Failed to load available barbers.'
    } finally {
      loadingBarbers.value = false
    }
  }
})

const showPaymentModal = ref(false)
const createdBooking = ref(null)

const errorMessage = ref('')

const showCalendar = ref(false)
const todayDate = new Date()
const weekDays = ['S', 'M', 'T', 'W', 'T', 'F', 'S']
const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
]
const currentMonth = ref(todayDate.getMonth())
const currentYear = ref(todayDate.getFullYear())

const calendarDays = computed(() => {
  const year = currentYear.value
  const month = currentMonth.value
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const daysInMonth = lastDay.getDate()
  const startingDay = firstDay.getDay()
  const days = Array(42).fill(null)
  for (let i = 0; i < daysInMonth; i++) {
    days[i + startingDay] = new Date(year, month, i + 1)
  }
  return days
})

function prevMonth() {
  if (currentMonth.value === 0) {
    currentMonth.value = 11
    currentYear.value--
  } else {
    currentMonth.value--
  }
}
function nextMonth() {
  if (currentMonth.value === 11) {
    currentMonth.value = 0
    currentYear.value++
  } else {
    currentMonth.value++
  }
}
function isPastDate(date) {
  if (!date) return true
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  return date < today
}
function formatDateLocal(date) {
  // Formats a JS Date object as YYYY-MM-DD in local time
  const yyyy = date.getFullYear();
  const mm = String(date.getMonth() + 1).padStart(2, '0');
  const dd = String(date.getDate()).padStart(2, '0');
  return `${yyyy}-${mm}-${dd}`;
}
function isSelectedDate(date) {
  if (!date || !form.value.date) return false
  return formatDateLocal(date) === form.value.date
}
function selectDate(date) {
  if (!date || isPastDate(date)) return
  form.value.date = formatDateLocal(date)
  showCalendar.value = false
}

const submitForm = async () => {
  // Validate date format
  const datePattern = /^\d{4}-\d{2}-\d{2}$/;
  if (!datePattern.test(form.value.date)) {
    errorMessage.value = 'Date must be in YYYY-MM-DD format (e.g., 2025-05-23).';
    return;
  }
  form.value.processing = true
  errorMessage.value = ''
  try {
    const response = await fetch('/booking', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        service: form.value.service,
        booking_date: form.value.date,
        booking_time: form.value.time,
        barber_id: form.value.barber_id,
        notes: form.value.notes,
        user_id: form.value.user_id,
        skip_payment: form.value.skip_payment
      })
    })
    if (response.redirected) {
      window.location.href = response.url
      return
    }
    if (!response.ok) {
      let data = {}
      try {
        data = await response.json()
      } catch (e) {
        errorMessage.value = 'Booking failed'
        return
      }
      if (response.status === 409 && data.error) {
        errorMessage.value = data.error
      } else {
        errorMessage.value = data.message || 'Booking failed'
      }
      return
    }
    const data = await response.json()
    createdBooking.value = data.booking

    // Only show payment modal if not skipping payment
    if (!data.skip_payment) {
      showPaymentModal.value = true
    }

    // Reset form
    form.value = {
      service: '',
      date: '',
      time: '',
      barber_id: '',
      notes: '',
      processing: false,
      user_id: '',
      skip_payment: false
    }
  } catch (e) {
    console.error('Booking error:', e)
    errorMessage.value = e.message || 'Booking failed. Please try again.'
  } finally {
    form.value.processing = false
  }
}

const handlePaymentSuccess = () => {
  showPaymentModal.value = false
  window.location.href = '/customer/bookings'
}

// Mobile dropdown functions
const getSelectedServiceName = () => {
  const service = props.services.find(s => s.slug === form.value.service)
  return service ? `${service.name} - £${service.price}` : ''
}

const selectService = (service) => {
  form.value.service = service.slug
  showServiceDropdown.value = false
}

const selectTime = (time) => {
  form.value.time = time
  showTimeDropdown.value = false
}

// Toggle functions for dropdowns
const toggleServiceDropdown = () => {
  showServiceDropdown.value = !showServiceDropdown.value
  if (showServiceDropdown.value) {
    showTimeDropdown.value = false
    showCalendar.value = false
  }
}

const toggleTimeDropdown = () => {
  showTimeDropdown.value = !showTimeDropdown.value
  if (showTimeDropdown.value) {
    showServiceDropdown.value = false
    showCalendar.value = false
  }
}

const toggleCalendar = () => {
  showCalendar.value = !showCalendar.value
  if (showCalendar.value) {
    showServiceDropdown.value = false
    showTimeDropdown.value = false
  }
}

// Close dropdowns and calendar when clicking outside
const handleClickOutside = (event) => {
  if (serviceDropdownRef.value && !serviceDropdownRef.value.contains(event.target)) {
    showServiceDropdown.value = false
  }
  if (timeDropdownRef.value && !timeDropdownRef.value.contains(event.target)) {
    showTimeDropdown.value = false
  }
  if (calendarRef.value && !calendarRef.value.contains(event.target)) {
    showCalendar.value = false
  }
}

// Clean up listener when component unmounts
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Add this new function for better date display
const formatDisplayDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-GB', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const currentStep = computed(() => {
  if (!form.service) return 1;
  if (!form.date || !form.time) return 2;
  if (!form.barber_id) return 3;
  return 4;
});
const currentStepLabel = computed(() => {
  if (!form.service) return 'Service';
  if (!form.date || !form.time) return 'Date & Time';
  if (!form.barber_id) return 'Barber';
  return 'Confirm';
});
const stepProgress = computed(() => {
  if (!form.service) return 0;
  if (!form.date || !form.time) return 33;
  if (!form.barber_id) return 66;
  return 100;
});

const showCustomerDropdown = ref(false)
const customerDropdownRef = ref(null)
const toggleCustomerDropdown = () => { showCustomerDropdown.value = !showCustomerDropdown.value }
const selectCustomer = (user) => {
  console.log('Selecting user:', user)
  form.value.user_id = String(user.id)
  console.log('form.value.user_id after select:', form.value.user_id)
  showCustomerDropdown.value = false
}
const getSelectedCustomerName = () => {
  const user = availableUsers.value.find(u => String(u.id) === String(form.value.user_id))
  return user ? `${user.name} (${user.email})` : ''
}
function handleClickOutsideCustomerDropdown(event) {
  if (customerDropdownRef.value && !customerDropdownRef.value.contains(event.target)) {
    showCustomerDropdown.value = false
  }
}
onMounted(() => {
  document.addEventListener('mousedown', handleClickOutsideCustomerDropdown)
})
onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutsideCustomerDropdown)
})
</script>

<style>
/* Base select styling for desktop */
.mobile-select {
  font-size: 16px;
  padding: 12px 16px;
  line-height: 1.5;
}

/* Compact container with large readable options */
@media (max-width: 768px) {
  .mobile-select {
    font-size: 16px !important;
    padding: 10px 16px 10px 12px !important;
    min-height: 42px !important;
    line-height: 1.4 !important;
    border-width: 1px !important;
    font-weight: 500 !important;
  }

  .mobile-select option {
    font-size: 22px !important;
    padding: 16px 14px !important;
    line-height: 1.3 !important;
    min-height: 52px !important;
    color: #000000 !important;
    background: white !important;
    font-weight: 700 !important;
  }
}

/* Even larger options for very small screens */
@media (max-width: 480px) {
  .mobile-select {
    font-size: 17px !important;
    padding: 11px 18px 11px 12px !important;
    min-height: 44px !important;
    font-weight: 500 !important;
  }

  .mobile-select option {
    font-size: 24px !important;
    padding: 18px 16px !important;
    min-height: 56px !important;
    font-weight: 700 !important;
  }
}

/* Chrome Mobile specific - force system appearance */
@supports (appearance: none) {
  @media (max-width: 768px) {
    .mobile-select {
      appearance: menulist !important;
      -webkit-appearance: menulist !important;
      -moz-appearance: menulist !important;
      background: white !important;
      font-size: 20px !important;
    }
  }
}

/* iOS Safari - prevent text size adjustment issues */
@supports (-webkit-touch-callout: none) {
  @media (max-width: 768px) {
    .mobile-select {
      -webkit-text-size-adjust: none !important;
      text-size-adjust: none !important;
      font-size: 16px !important;
    }

    .mobile-select option {
      -webkit-text-size-adjust: none !important;
      text-size-adjust: none !important;
      font-size: 22px !important;
    }
  }
}

/* Android Chrome - minimal scaling */
@media screen and (max-width: 768px) and (-webkit-min-device-pixel-ratio: 1) {
  .mobile-select {
    zoom: 1.05 !important;
    transform: scale(1.02) !important;
    transform-origin: left top !important;
    margin-bottom: 4px !important;
  }
}

/* Universal mobile touch device fallback */
@media (hover: none) and (pointer: coarse) {
  .mobile-select {
    font-size: 16px !important;
    min-height: 42px !important;
    font-weight: 500 !important;
  }

  .mobile-select option {
    font-size: 22px !important;
    min-height: 52px !important;
    font-weight: 700 !important;
    padding: 16px 14px !important;
  }
}
</style>
