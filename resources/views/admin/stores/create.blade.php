<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Create Store & Manager</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <!-- Leaflet Map CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <style>
    .brand-link { background-color: #0c831f !important; }
    #storeMap { height: 320px; width: 100%; border-radius: 12px; border: 2px solid #0c831f; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/stores" class="nav-link">Stores</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Create Store</a></li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4 position-fixed">
    <a href="/admin" class="brand-link text-center">
      <span class="brand-text font-weight-bold text-white"><i class="fas fa-bolt mr-2"></i>Q-Commerce</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item"><a href="/admin" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="/admin/stores" class="nav-link active"><i class="nav-icon fas fa-store"></i><p>Stores & Managers</p></a></li>
          <li class="nav-item"><a href="/admin/products" class="nav-link"><i class="nav-icon fas fa-boxes"></i><p>Product Catalog</p></a></li>
          <li class="nav-item"><a href="/admin/store-manager" class="nav-link"><i class="nav-icon fas fa-user-cog"></i><p>Store Manager Portal</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0 font-weight-bold">Add New Store & Interactive Map Location</h1>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card card-success">
          <div class="card-header"><h3 class="card-title font-weight-bold">Store Location & Manager Credentials</h3></div>
          <form action="/admin/stores/store" method="POST">
            @csrf
            <div class="card-body">
              <h5 class="text-success font-weight-bold mb-3"><i class="fas fa-store mr-1"></i> Store Information</h5>
              <div class="row">
                <div class="col-md-6 form-group">
                  <label>Store Name</label>
                  <input type="text" name="name" class="form-control" placeholder="e.g. Krishnanagar Branch" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Store Code</label>
                  <input type="text" name="code" class="form-control" placeholder="e.g. STR-KRN-03" required>
                </div>
              </div>
              <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" id="addressInput" class="form-control" placeholder="Street address" required>
              </div>

              <!-- Interactive Location Picker Map & Delivery Radius Slider -->
              <div class="card card-outline card-success mb-4">
                <div class="card-header">
                  <h3 class="card-title font-weight-bold"><i class="fas fa-map-marker-alt text-danger mr-2"></i>Select Exact Location on Map & Delivery Radius</h3>
                </div>
                <div class="card-body">
                  <p class="text-muted mb-2"><i class="fas fa-info-circle mr-1"></i> Drag the red pointer or click on the map to mark the exact store location.</p>
                  
                  <div id="storeMap"></div>

                  <div class="row mt-3">
                    <div class="col-md-6 form-group">
                      <label>Latitude</label>
                      <input type="text" name="latitude" id="latInput" class="form-control" value="23.4013" readonly required>
                    </div>
                    <div class="col-md-6 form-group">
                      <label>Longitude</label>
                      <input type="text" name="longitude" id="lngInput" class="form-control" value="88.5010" readonly required>
                    </div>
                  </div>

                  <!-- Delivery Radius Range Slider -->
                  <div class="form-group mt-2">
                    <label class="font-weight-bold text-dark d-flex justify-content-between">
                      <span><i class="fas fa-circle-notch text-success mr-1"></i> Delivery Radius:</span>
                      <span class="badge badge-success px-3 py-2 text-md" id="radiusBadge">5.0 km</span>
                    </label>
                    <input type="range" class="custom-range" name="delivery_radius_km" id="radiusSlider" min="1" max="25" step="0.5" value="5">
                    <input type="hidden" name="radius_value" id="radiusValueInput" value="5.0">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 form-group">
                  <label>City</label>
                  <input type="text" name="city" class="form-control" value="Krishnanagar" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Pincode</label>
                  <input type="text" name="pincode" class="form-control" value="741101" required>
                </div>
              </div>

              <hr>

              <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-user-shield mr-1"></i> Store Manager Portal Credentials</h5>
              <div class="row">
                <div class="col-md-4 form-group">
                  <label>Manager Full Name</label>
                  <input type="text" name="manager_name" class="form-control" placeholder="Manager Name" required>
                </div>
                <div class="col-md-4 form-group">
                  <label>Manager Email (Login ID)</label>
                  <input type="email" name="manager_email" class="form-control" placeholder="manager@blinkit.com" required>
                </div>
                <div class="col-md-4 form-group">
                  <label>Manager Password</label>
                  <input type="password" name="manager_password" class="form-control" placeholder="******" required>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-save mr-1"></i> Save Store & Create Manager</button>
              <a href="/admin/stores" class="btn btn-default">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin.</strong></footer>
</div>

<!-- jQuery & AdminLTE -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        color: '#0c831f',
        fillColor: '#0c831f',
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
