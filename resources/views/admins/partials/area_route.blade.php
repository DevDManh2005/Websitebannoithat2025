@php
  $__area = request()->routeIs('staff.*') ? 'staff' : 'admin';
  $__r = function(string $suffix, array $params = []) use ($__area) {
      return route($__area . '.' . $suffix, $params);
  };
@endphp
