<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Add New Store & Interactive Map Location</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <!-- Leaflet Map CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <style>
    #storeMap { height: 380px; width: 100%; border-radius: 8px; border: 2px solid #28a745; }
    .location-card { border-left: 4px solid #28a745; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/stores" class="nav-link">Stores</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Add New Store & Interactive Map Location</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
          <h1 class="m-0 font-weight-bold"><i class="fas fa-store text-success mr-2"></i>Add New Store Branch & Interactive Map Location</h1>
          <p class="text-muted mb-0 small">Setup store info, precise GPS coordinates, delivery radius, operational rules, and manager login.</p>
        </div>
        <a href="/admin/stores" class="btn btn-default font-weight-bold"><i class="fas fa-arrow-left mr-1"></i> Back to Stores</a>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <form action="/admin/stores/store" method="POST">
          @csrf
          
          <div class="row">
            <!-- LEFT COLUMN: STORE DETAILS & MANAGER CREDENTIALS -->
            <div class="col-lg-7">
              
              <!-- 1. GENERAL STORE INFORMATION -->
              <div class="card card-outline card-success shadow-sm mb-4">
                <div class="card-header">
                  <h3 class="card-title font-weight-bold"><i class="fas fa-info-circle text-success mr-2"></i>Store Information & Location Address</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>Store Name <span class="text-danger">*</span></label>
                      <input type="text" name="name" class="form-control" placeholder="e.g. Krishnanagar Main Hub" required>
                    </div>
                    <div class="col-md-6 form-group">
                      <label>Store Code <span class="text-danger">*</span></label>
                      <input type="text" name="code" class="form-control" placeholder="e.g. STR-KRN-01" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Full Address <span class="text-danger">*</span></label>
                    <input type="text" name="address" id="addressInput" class="form-control" placeholder="Street address, building name, landmark..." required>
                  </div>

                  <div class="row">
                    <div class="col-md-3 form-group">
                      <label>City <span class="text-danger">*</span></label>
                      <input type="text" name="city" class="form-control" value="Krishnanagar" required>
                    </div>
                    <div class="col-md-3 form-group">
                      <label>Pincode <span class="text-danger">*</span></label>
                      <input type="text" name="pincode" class="form-control" value="741101" required>
                    </div>
                    <div class="col-md-3 form-group">
                      <label>Contact Phone</label>
                      <input type="text" name="store_phone" class="form-control" placeholder="+91 9876543210">
                    </div>
                    <div class="col-md-3 form-group">
                      <label>Store GST Number <small class="text-muted">(Optional)</small></label>
                      <input type="text" name="gstin" class="form-control" placeholder="e.g. 19ABCDE1234F1ZH">
                    </div>
                  </div>
                </div>
              </div>

              <!-- 2. STORE OPERATIONAL RULES & TIMINGS -->
              <div class="card card-outline card-info shadow-sm mb-4">
                <div class="card-header">
                  <h3 class="card-title font-weight-bold"><i class="fas fa-clock text-info mr-2"></i>Branch Operational Hours & Order Rules</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-3 form-group">
                      <label>Opening Time</label>
                      <input type="time" name="opening_time" class="form-control" value="06:00" required>
                    </div>
                    <div class="col-md-3 form-group">
                      <label>Closing Time</label>
                      <input type="time" name="closing_time" class="form-control" value="23:00" required>
                    </div>
                    <div class="col-md-3 form-group">
                      <label>Min Order (₹)</label>
                      <input type="number" step="0.01" name="min_order_amount" class="form-control" value="0" required>
                    </div>
                    <div class="col-md-3 form-group">
                      <label>Delivery Speed (Mins)</label>
                      <input type="number" name="estimated_delivery_time_mins" class="form-control" value="15" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>Standard Delivery Fee (₹)</label>
                      <input type="number" step="0.01" name="delivery_fee" class="form-control" value="15.00" required>
                    </div>
                    <div class="col-md-6 form-group">
                      <label>Free Delivery Threshold (₹)</label>
                      <input type="number" step="0.01" name="free_delivery_threshold" class="form-control" value="299.00" required>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 3. STORE MANAGER CREDENTIALS -->
              <div class="card card-outline card-primary shadow-sm mb-4">
                <div class="card-header">
                  <h3 class="card-title font-weight-bold"><i class="fas fa-user-shield text-primary mr-2"></i>Assigned Manager Credentials</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4 form-group">
                      <label>Manager Name <span class="text-danger">*</span></label>
                      <input type="text" name="manager_name" class="form-control" placeholder="Manager Name" required>
                    </div>
                    <div class="col-md-4 form-group">
                      <label>Login Email <span class="text-danger">*</span></label>
                      <input type="email" name="manager_email" class="form-control" placeholder="manager@sbmartquick.com" required>
                    </div>
                    <div class="col-md-4 form-group">
                      <label>Login Password <span class="text-danger">*</span></label>
                      <input type="password" name="manager_password" class="form-control" placeholder="******" required>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- RIGHT COLUMN: INTERACTIVE MAP & GPS LOCATION PICKER -->
            <div class="col-lg-5">
              <div class="card card-outline card-success shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-light">
                  <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-map-marked-alt text-danger mr-2"></i>Interactive GPS Map Location Picker</h3>
                </div>
                <div class="card-body">
                  <p class="text-muted small mb-2"><i class="fas fa-mouse-pointer text-success mr-1"></i> Click or drag the marker to pinpoint the exact store location.</p>
                  
                  <div id="storeMap"></div>

                  <div class="row mt-3">
                    <div class="col-md-6 form-group">
                      <label class="small text-muted font-weight-bold">Latitude</label>
                      <input type="text" name="latitude" id="latInput" class="form-control form-control-sm font-weight-bold" value="23.4013" placeholder="e.g. 23.4013" required>
                    </div>
                    <div class="col-md-6 form-group">
                      <label class="small text-muted font-weight-bold">Longitude</label>
                      <input type="text" name="longitude" id="lngInput" class="form-control form-control-sm font-weight-bold" value="88.5010" placeholder="e.g. 88.5010" required>
                    </div>
                    <div class="col-12 form-group mb-2">
                      <button type="button" id="detectLocBtn" class="btn btn-outline-success btn-sm btn-block font-weight-bold shadow-sm">
                        <i class="fas fa-crosshairs mr-1"></i> Detect & Pin Entered Location on Map
                      </button>
                    </div>
                  </div>

                  <!-- DELIVERY RADIUS RANGE SLIDER -->
                  <div class="form-group mt-2">
                    <label class="font-weight-bold text-dark d-flex justify-content-between">
                      <span><i class="fas fa-circle-notch text-success mr-1"></i> Delivery Coverage Radius:</span>
                      <span class="badge badge-success px-3 py-1" id="radiusBadge">5.0 km</span>
                    </label>
                    <input type="range" class="custom-range" name="delivery_radius_km" id="radiusSlider" min="1" max="30" step="0.5" value="5">
                    <input type="hidden" name="radius_value" id="radiusValueInput" value="5.0">
                  </div>

                  <div class="alert alert-light border small text-muted mb-0">
                    <i class="fas fa-shield-alt text-success mr-1"></i> Customer orders will automatically route to this store if within the selected delivery radius.
                  </div>
                </div>
                <div class="card-footer bg-white">
                  <button type="submit" class="btn btn-success btn-lg btn-block font-weight-bold shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> Save Store Branch & Assign Manager
                  </button>
                  <a href="/admin/stores" class="btn btn-default btn-block mt-2">Cancel</a>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin.</strong></footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- Leaflet Map JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  $(document).ready(function() {
    var defaultLat = 23.4013;
    var defaultLng = 88.5010;
    var defaultRadiusKm = 5.0;

    // Initialize Leaflet Map
    var map = L.map('storeMap').setView([defaultLat, defaultLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Draggable Store Pointer Marker
    var marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
    
    // Delivery Radius Circle
    var circle = L.circle([defaultLat, defaultLng], {
        color: '#28a745',
        fillColor: '#28a745',
        fillOpacity: 0.2,
        radius: defaultRadiusKm * 1000
    }).addTo(map);

    function updateMapLocation(lat, lng) {
      document.getElementById('latInput').value = lat.toFixed(6);
      document.getElementById('lngInput').value = lng.toFixed(6);
      circle.setLatLng([lat, lng]);
    }

    marker.on('dragend', function(e) {
      var latLng = marker.getLatLng();
      updateMapLocation(latLng.lat, latLng.lng);
    });

    map.on('click', function(e) {
      marker.setLatLng(e.latlng);
      updateMapLocation(e.latlng.lat, e.latlng.lng);
    });

    function detectAndPinLocation() {
      var latVal = parseFloat($('#latInput').val());
      var lngVal = parseFloat($('#lngInput').val());
      if (!isNaN(latVal) && !isNaN(lngVal) && latVal >= -90 && latVal <= 90 && lngVal >= -180 && lngVal <= 180) {
        var newLatLng = new L.LatLng(latVal, lngVal);
        marker.setLatLng(newLatLng);
        circle.setLatLng(newLatLng);
        map.flyTo(newLatLng, 15);
      } else {
        alert('Please enter valid numerical Latitude and Longitude values.');
      }
    }

    $('#detectLocBtn').on('click', function() {
      detectAndPinLocation();
    });

    $('#latInput, #lngInput').on('change', function() {
      var latVal = parseFloat($('#latInput').val());
      var lngVal = parseFloat($('#lngInput').val());
      if (!isNaN(latVal) && !isNaN(lngVal) && latVal >= -90 && latVal <= 90 && lngVal >= -180 && lngVal <= 180) {
        var newLatLng = new L.LatLng(latVal, lngVal);
        marker.setLatLng(newLatLng);
        circle.setLatLng(newLatLng);
        map.panTo(newLatLng);
      }
    });

    // Slider Event Listener for Delivery Radius
    $('#radiusSlider').on('input change', function() {
      var radiusKm = parseFloat($(this).val());
      $('#radiusBadge').text(radiusKm.toFixed(1) + ' km');
      $('#radiusValueInput').val(radiusKm);
      circle.setRadius(radiusKm * 1000);
    });
  });
</script>
</body>
</html>
