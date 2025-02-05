$(document).ready(function () {
    const regionDropdown = $('#region');
    const provinceDropdown = $('#province');
    const cityDropdown = $('#city');
    const barangayDropdown = $('#barangay');

    // Load regions
    $.getJSON('assets/ph-json/region.json', function (data) {
        regionDropdown.append('<option selected disabled>Choose Region</option>');
        $.each(data, function (key, entry) {
            regionDropdown.append($('<option></option>').attr('value', entry.region_code).text(entry.region_name));
        });
    }).fail(function () {
        console.log("Failed to load region data.");
    });

    // Load provinces when region changes
    regionDropdown.on('change', function () {
        let selectedRegion = $(this).val();
        provinceDropdown.empty().append('<option selected disabled>Choose Province</option>');
        cityDropdown.empty().append('<option selected disabled>Choose City/Municipality</option>');
        barangayDropdown.empty().append('<option selected disabled>Choose Barangay</option>');

        $.getJSON('assets/ph-json/province.json', function (data) {
            let filteredProvinces = data.filter(p => p.region_code === selectedRegion);
            $.each(filteredProvinces, function (key, entry) {
                provinceDropdown.append($('<option></option>').attr('value', entry.province_code).text(entry.province_name));
            });
        }).fail(function () {
            console.log("Failed to load province data.");
        });
    });

    // Load cities when province changes
    provinceDropdown.on('change', function () {
        let selectedProvince = $(this).val();
        cityDropdown.empty().append('<option selected disabled>Choose City/Municipality</option>');
        barangayDropdown.empty().append('<option selected disabled>Choose Barangay</option>');

        $.getJSON('assets/ph-json/city.json', function (data) {
            let filteredCities = data.filter(c => c.province_code === selectedProvince);
            $.each(filteredCities, function (key, entry) {
                cityDropdown.append($('<option></option>').attr('value', entry.city_code).text(entry.city_name));
            });
        }).fail(function () {
            console.log("Failed to load city data.");
        });
    });

    // Load barangays when city changes
    cityDropdown.on('change', function () {
        let selectedCity = $(this).val();
        barangayDropdown.empty().append('<option selected disabled>Choose Barangay</option>');

        $.getJSON('assets/ph-json/barangay.json', function (data) {
            let filteredBarangays = data.filter(b => b.city_code === selectedCity);
            $.each(filteredBarangays, function (key, entry) {
                barangayDropdown.append($('<option></option>').attr('value', entry.brgy_code).text(entry.brgy_name));
            });
        }).fail(function () {
            console.log("Failed to load barangay data.");
        });
    });
});
