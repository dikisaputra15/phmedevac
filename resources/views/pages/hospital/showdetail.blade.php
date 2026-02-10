@extends('layouts.master')

@section('title','More Details')
@section('page-title', 'Papua New Guinea Medical Facility')

@push('styles')

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/1.6.0/Control.FullScreen.css" />

<style>
    #map {
        height: 600px;
    }

    p{
        margin-bottom: 8px;
        line-height: 18px;
    }

    .btn-danger{
        background-color:#395272;
        border-color: transparent;
    }

    .btn-danger:hover{
        background-color:#5686c3;
        border-color: transparent;
    }

    .btn.active {
        background-color: #5686c3 !important;
        border-color: transparent !important;
        color: #fff !important;
    }

    .p-3{
        padding: 10px !important;
        margin: 0 3px;
    }

    .btn-outline-danger{
        color: #FFFFFF;
        background-color:#395272;
        border-color: transparent;
    }
     .btn-outline-danger:hover{
        background-color:#5686c3;
        border-color: transparent;
    }

    .fa,
    .fab,
    .fad,
    .fal,
    .far,
    .fas {
        color: #346abb;
    }

    .card-header{
        padding: 0.25rem 1.25rem;
        color: #3c66b5;
        font-weight: bold;
    }

    .mb-4{
        margin-bottom: 0.5rem !important;
    }

     .leaflet-routing-container-hide .leaflet-routing-collapse-btn
    {
        left: 8px;
        top: 8px;
    }

    .leaflet-control-container .leaflet-routing-container-hide {
        width: 48px;
        height: 48px;
    }

    /* Classification section */
    .classification {
      display: flex;
      width: 100%;
    }

    .class-column {
      flex: 1;
      text-align: center;

    }
    .class-column:last-child {
      border-right: none;
    }

    .class-header {
      font-weight: 600;
      padding: 0.1rem 0;
    }

    /* Color bars */
    .class-medical-classification {border: none; text-align: center;}
    .class-airport-category {border: none;}
    .class-advanced { border-bottom: 3px solid #0070c0; }
    .class-intermediate { border-bottom: 3px solid #00b050; }
    .class-basic { border-bottom: 3px solid #ffc000; }

    /* Hospital layout */
    .hospital-list {
      display: flex;
      flex-direction: column;
      align-items: center;

    }

    /* For side-by-side classes */
    .hospital-row {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0;
    }

    .hospital-item {
      display: flex;
      align-items: center;
      gap: 0;
      font-size: 0.9rem;
      white-space: nowrap;
    }

    .hospital-icon {
      width: 18px;
      height: 18px;
      border-radius: 3px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    /* Image inside icon box */
    .hospital-icon img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    /* Airfield icons */
    .category-item img {
      width: 16px;
      height: 16px;
      object-fit: contain;
    }

    #level11Modal .modal-body {
            max-height: 50vh;
            overflow-y: auto;
        }
</style>

@endpush

@section('conten')

<div class="card">

    <div class="d-flex justify-content-between p-3" style="background-color: #dfeaf1;">

        <div class="d-flex flex-column gap-1">
            <h2 class="fw-bold mb-0">{{ $hospital->name }}</h2>
            <span class="fw-bold"><b>Global Classification:</b> {{ $hospital->facility_category }} | <b>Country Classification:</b> {{ $hospital->facility_level === 'Primary Care Facility'
                ? 'Primary Care Facility (Level A)'
                : $hospital->facility_level
            }}
            </span>
        </div>

        <div class="d-flex gap-2 ms-auto">

            <a href="{{ url('hospitals') }}/{{$hospital->id}}" class="btn btn-outline-danger d-flex flex-column align-items-center p-3 {{ request()->is('hospitals/'.$hospital->id) ? 'active' : '' }}">
                <img src="{{ asset('images/icon-menu-general-info.png') }}" style="width: 18px; height: 24px;">
                <small>General</small>
            </a>

            <a href="{{ url('hospitals/clinic') }}/{{$hospital->id}}" class="btn btn-outline-danger d-flex flex-column align-items-center p-3 {{ request()->is('hospitals/clinic/'.$hospital->id) ? 'active' : '' }}">
                <img src="{{ asset('images/icon-menu-medical-facility-white.png') }}" style="width: 18px; height: 24px;">
                <small>Clinical</small>
            </a>

            <a href="{{ url('hospitals/emergency') }}/{{$hospital->id}}" class="btn btn-outline-danger d-flex flex-column align-items-center p-3 {{ request()->is('hospitals/emergency/'.$hospital->id) ? 'active' : '' }}">
                <img src="{{ asset('images/icon-emergency-support-white.png') }}" style="width: 24px; height: 24px;">
                <small>Emergency</small>
            </a>

            <a href="{{ url('hospital') }}" class="btn btn-danger d-flex flex-column align-items-center p-3 {{ request()->is('hospital') ? 'active' : '' }}">
                 <img src="{{ asset('images/icon-medical.png') }}" style="width: 24px; height: 24px;">
                <small>Medical</small>
            </a>

            <a href="{{ url('airports') }}" class="btn btn-danger d-flex flex-column align-items-center p-3 {{ request()->is('airports') ? 'active' : '' }}">
                <i class="bi bi-airplane fs-3"></i>
                <small>Airports</small>
            </a>

            <a href="{{ url('aircharter') }}" class="btn btn-danger d-flex flex-column align-items-center p-3 {{ request()->is('aircharter') ? 'active' : '' }}">
                 <img src="{{ asset('images/icon-air-charter.png') }}" style="width: 48px; height: 24px;">
                <small>Air Charter</small>
            </a>

            <a href="{{ url('embassiees') }}" class="btn btn-danger d-flex flex-column align-items-center p-3 {{ request()->is('embassiees') ? 'active' : '' }}">
            <img src="{{ asset('images/icon-embassy.png') }}" style="width: 24px; height: 24px;">
                <small>Embassies</small>
            </a>
        </div>
    </div>

    <div class="card mb-4 position-relative">
        <div class="card-body" style="padding:0 7px;">
            <small><i>Last Updated {{ $hospital->created_at->format('M Y') }}</i></small>

            @role('admin')
            <a href="{{ route('hospitaldata.edit', $hospital->id) }}"
            style="position:absolute; right:7px;" title="edit">
                <i class="fas fa-edit"></i>
            </a>
            @endrole
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header fw-bold"><img src="{{ asset('images/icon-general-info.png') }}" style="width: 24px; height: 24px;"> General Medical Facility Info</div>
                <div class="card-body overflow-auto">
                    <p>
                        <strong>Status:</strong> {{ $hospital->status }}
                    </p>
                    <p>
                        <strong>Number Of Beds:</strong> {{ $hospital->number_of_beds }}
                    </p>
                    <p>
                        <strong>Population Catchment:</strong> {{ $hospital->population_catchment }}
                    </p>
                    <p>
                        <strong>Ownership:</strong> {{ $hospital->ownership }}
                    </p>
                    <p>
                        <strong>Hours Of Operation:</strong><br>
                        <?php echo $hospital->hrs_of_operation; ?>
                    </p>
                    <p>
                        <strong>Note:</strong>
                        <?php echo $hospital->others; ?>
                    </p>
                    <p>
                        <strong>Medical Services Info:</strong> <?php echo $hospital->other_medical_info; ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header fw-bold"><img src="{{ asset('images/icon-location.png') }}" style="width: 18px; height: 24px;"> Location</div>
                <div class="card-body overflow-auto">
                    <p>
                        <strong>Address:</strong>
                        {{ $hospital->address }},
                        {{ $subcity->sub_city }},
                        {{ $city->city }},
                        {{ $province->provinces_region }}, Philippines
                    </p>
                    <p>
                        <strong>Latitude:</strong> {{ $hospital->latitude }}
                    </p>
                    <p>
                        <strong>Longitude:</strong> {{ $hospital->longitude }}
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold"><img src="{{ asset('images/contact-icon.png') }}" style="width: 24px; height: 24px;"> Contact Details</div>
                <div class="card-body overflow-auto">
                    <p>
                        <strong>Telephone:</strong> <?php echo $hospital->telephone; ?>
                    </p>
                    <p>
                        <strong>Fax:</strong> <?php echo $hospital->fax; ?>
                    </p>
                    <p>
                        <strong>Email:</strong> <?php echo $hospital->email; ?>
                    </p>
                    <p>
                        <strong>Website:</strong> <?php echo $hospital->website; ?>
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold"><img src="{{ asset('images/icon-nearest-accomodation.png') }}" style="width: 24px; height: 18px;">  Nearest Accommodation</div>
                <div class="card-body overflow-auto">
                    <?php echo $hospital->nearest_accommodation; ?>
                </div>
            </div>

        </div>

        <div class="col-md-6">
            <div class="card">

            <div class="classification" style="flex-direction: column; width:100%;">
                      <div class="class-header class-medical-classification">Medical Facility Classification</div>
                      <div class="classification">
                        <!-- Advanced -->
                        <div class="class-column">
                          <div class="class-header class-advanced">Advanced</div>
                          <div class="hospital-list">
                            <div class="hospital-item">
                              <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level66Modal">
                                <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital-pin-red.png" style="width:24px; height:24px;">
                                <small>Level 3</small>
                              </button>
                            </div>
                          </div>
                        </div>

                        <!-- Intermediate -->
                        <div class="class-column">
                          <div class="class-header class-intermediate">Intermediate</div>
                          <div class="hospital-list">
                            <div class="hospital-row">
                              <div class="hospital-item">
                                <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level55Modal">
                                  <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-blue.png" style="width:24px; height:24px;">
                                  <small>Level 2</small>
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Basic -->
                        <div class="class-column">
                          <div class="class-header class-basic">Basic</div>
                          <div class="hospital-list">
                            <div class="hospital-row">
                              <div class="hospital-item">
                                <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level44Modal">
                                  <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-purple.png" style="width:24px; height:24px;">
                                  <small>Level 1</small>
                                </button>
                              </div>
                              <div class="hospital-item">
                                <button class="btn p-1" data-bs-toggle="modal" data-bs-target="#level11Modal">
                                    <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-tosca.png" style="width:24px; height:24px;">
                                    <small>Primary Care Facility (Level A)</small>
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                <div class="card-body p-0">
                    <div id="map"></div>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="modal fade" id="level11Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:800px;">
    <div class="modal-content">
      <div class="modal-header">
         <div class="d-flex align-items-center">
            <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-tosca.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Primary Care Facility (PCF)</h5>
         </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="p-modal text-justify">
            <b>Primary Care Facility</b> – a first-contact healthcare facility that offers basic services including emergency services and provision for normal deliveries, subdivided into:
        </p>

        <ol class="p-modal text-justify" type="a">
            <li>
                <b>With in-patient beds</b> – a short stay facility where patients can be admitted for a short period of 1 to 3 days, this includes infirmary, dispensary, and birthing home.
            </li>

            <li>
                <b>Without beds</b> – a facility where medical and/or dental examination and treatment and minor surgical procedures are rendered without confining the patient. This includes the following:

                <ol type="1">
                    <li>Medical Outpatient Clinic, Overseas Filipino Workers (OFW) Clinics, and Dental Clinics.</li>

                    <li>
                        <b>Infirmary</b> – a healthcare facility with in-patient beds capable of providing diagnosis and treatment of medical conditions and simple surgical procedures but lacks one or several components required of a hospital, including operating room and/or intensive care unit.
                    </li>

                    <li>
                        <b>Dispensary</b> – a healthcare facility where medicine or medical treatment is dispensed. Under the new DOH classification (AO 2012-0012), they are considered a primary care facility with in-patient beds.
                    </li>

                    <li>
                        <b>Birthing Home</b> – a facility with in-patient beds that provides maternity services (pre-natal, normal spontaneous delivery, post-natal care) and newborn care. Also called a maternity clinic.
                    </li>

                    <li>
                        <b>Medical Outpatient Clinic</b> – an institution or facility providing medical outpatient health services such as diagnostic examination, treatment, and health counseling.
                    </li>

                    <li>
                        <b>Specialty Hospital</b> – a hospital that specializes in a particular disease or condition or in one type of patient, licensed as such with no corresponding level of classification. This includes children’s hospitals and orthopedic hospitals.
                    </li>

                    <li>
                        <b>Geographically Isolated and Disadvantaged Areas (GIDA)</b> – communities with marginalized populations that are physically and socio-economically separated from mainstream society and characterized by:
                        <ol type="a">
                            <li>
                                <b>Physical Factors</b> – isolated due to distance, weather conditions, and transportation difficulties (island, upland, lowland, landlocked, hard-to-reach and underserved communities).
                            </li>
                            <li>
                                <b>Socio-economic Factors</b> – high poverty incidence, presence of vulnerable sectors, communities in or recovering from crisis or armed conflict.
                            </li>
                        </ol>
                        <i>Note: These areas are identified by the DOH.</i>
                    </li>

                    <li>
                        <b>Health Human Resource Shortage (HHRS) areas</b> – areas with inadequate health human resources, determined based on accessibility, terrain, distance, availability of public transport, socio-economic factors, peace and order, and evaluation reports from PhilHealth Regional Offices, DOH offices, and the National Statistics Coordination Board.
                    </li>
                </ol>
            </li>
        </ol>

        <p class="p-modal text-justify">
            <b>Note:</b> This classification and service scope is aligned with the Philippine DOH hospital licensing and classification framework pursuant to the Hospital Licensure Act. <a href="{{ asset('files/ao-2012-0012.pdf') }}" target="_blank">Philippines Hospital and other medical facilities Classification (AO 2012-0012)</a>
        </p>

        </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="level2Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <div class="d-flex align-items-center">
                <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-orange.png" style="width:30px; height:30px;">
                <h5 class="modal-title" id="disclaimerLabel">Class 2</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p><b>Community Health Post - Health Sub Center (CHP)</b></p>
            <p class="p-modal">Primary health, ambulatory care, and short stay inpatient and maternity care at the local rural / remote community level, with a minimum of six (6) health workers to ensure safe 24-hour care and treatment.</p>
        </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="level3Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <div class="d-flex align-items-center">
                <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-green.png" style="width:30px; height:30px;">
                <h5 class="modal-title" id="disclaimerLabel">Class D — Sub-district Hospital</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p class="p-modal">Provides basic inpatient and emergency care with general practitioners and limited specialist support. Mainly located in sub-districts serving as the first referral point before higher-level hospitals.</p>
        </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="level44Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:800px;">
        <div class="modal-content">
        <div class="modal-header">
            <div class="d-flex align-items-center">
                <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-purple.png" style="width:30px; height:30px;">
                <h5 class="modal-title" id="disclaimerLabel">Level 1</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p class="p-modal text-justify">
                Level 1 hospitals in the Philippines provide core specialist care in internal medicine, surgery, obstetrics, and pediatrics in the Philippines under Department of Health (DOH) licensing standards. These hospitals manage common and uncomplicated medical conditions and provide first-line inpatient facilities and services, with defined referral pathways for complex cases to higher-level hospitals. Level 1 hospitals operate with a bed capacity of up to 50 beds.
            </p>
            <p class="p-modal text-justify">
                These facilities are equipped with basic but essential clinical and support services, including isolation facilities, maternity services, dental clinics, pharmacy services, and blood stations. Diagnostic capabilities include first-level diagnostic imaging (X-ray) and secondary-level clinical laboratory services with a consulting pathologist, ensuring safe and standardized primary hospital care.
            </p>
            <p class="p-modal text-justify">
                <b>Note:</b> This classification and service scope is aligned with the Philippine DOH hospital licensing and classification framework pursuant to the Hospital Licensure Act. <a href="{{ asset('files/ao-2012-0012.pdf') }}" target="__blank">Philippines Hospital and other medical facilities Classification (AO 2012-0012)</a>
            </p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level55Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
            <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital_pin-blue.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Level 2</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="p-modal text-justify">
           Level 2 hospitals provide broad specialist services with limited subspecialist capability, which function as regional referral centers in the Philippines under Department of Health (DOH) licensing standards. They are equipped to manage moderately complex medical and surgical cases and operate with a bed capacity of up to 100 beds, supported by core inpatient and procedural infrastructure.
        </p>
        <p class="p-modal text-justify">
            These facilities maintain departmentalized clinical services and essential critical care units, including respiratory units, Intensive Care Units (ICU), Neonatal Intensive Care Units (NICU), and High-Risk Pregnancy Units (HRPU). Diagnostic and treatment capabilities include operating theaters, clinical laboratory services, and second-level diagnostic imaging (X-ray), enabling comprehensive secondary-level hospital care.
        </p>
        <p class="p-modal text-justify">
            <b>Note:</b> This classification and service scope is aligned with the Philippine DOH hospital licensing and classification framework pursuant to the Hospital Licensure Act. <a href="{{ asset('files/ao-2012-0012.pdf') }}" target="__blank">Philippines Hospital and other medical facilities Classification (AO 2012-0012)</a>
        </p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="level66Modal" tabindex="-1" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
            <img src="https://pg.concordreview.com/wp-content/uploads/2025/01/hospital-pin-red.png" style="width:30px; height:30px;">
            <h5 class="modal-title" id="disclaimerLabel">Level 3</h5>
        </div>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="p-modal text-justify">
            Level 3 hospitals are the highest-tier medical facility in the Philippines under the Department of Health (DOH) licensing standards. Level 3 facilities provide comprehensive specialist and subspecialist services supported by advanced medical technology, tertiary diagnostic capabilities, and full referral-level clinical functions. These hospitals manage complex and severe medical cases and operate with a bed capacity exceeding 500 beds, consistent with requirements for tertiary hospital classification.
        </p>
        <p class="p-modal text-justify">
            In addition to full clinical services, Level 3 hospitals function as teaching, training, and research institutions. Their mandated service capabilities include physical medicine and rehabilitation, ambulatory surgery, dialysis services, tertiary-level laboratory facilities, blood banking capacity, and third-level diagnostic imaging (X-ray).
        </p>
        <p class="p-modal text-justify">
            <b>Note:</b> : This classification and service scope is aligned with the Philippine DOH hospital licensing and classification framework pursuant to the Hospital Licensure Act. <a href="{{ asset('files/ao-2012-0012.pdf') }}" target="__blank">Philippines Hospital and other medical facilities Classification (AO 2012-0012)</a>
        </p>
      </div>
    </div>
  </div>
</div>

@endsection

@push('service')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/1.6.0/Control.FullScreen.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const latitude = {{ $hospital->latitude }};
    const longitude = {{ $hospital->longitude }};
    const embassyName = '{{ $hospital->name }}';

    const map = L.map('map', {
        fullscreenControl: true
    }).setView([latitude, longitude], 17);

    // --- Define Tile Layers ---
    // 1. Street Map (OpenStreetMap)
    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18 // OSM generally goes up to zoom level 22
    });

    // 2. Satellite Map (Esri World Imagery) - Recommended, no API key needed
    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri',
        maxZoom: 18 // Esri World Imagery also typically goes up to zoom level 22
    });

    // Add the satellite layer to the map by default
    satelliteLayer.addTo(map);

    // --- Add Layer Control ---
    // Define the base layers that the user can switch between
   const baseLayers = {
        "Satelit Map": satelliteLayer,
        "Street Map": osmLayer
    };

    // Add the layer control to the map. This will appear in the top-right corner.
    L.control.layers(baseLayers).addTo(map);

    // Add a marker at the embassy's location
    L.marker([latitude, longitude])
        .addTo(map)
        .bindPopup(embassyName) // Display the embassy's name when the marker is clicked
        .openPopup(); // Automatically open the popup when the map loads
</script>
@endpush
