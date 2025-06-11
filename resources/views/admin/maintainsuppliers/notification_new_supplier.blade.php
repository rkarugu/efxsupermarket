<x-mail::message>
  # Welcome Aboard

  ![alt text]({{ env('SUPPLIER_PORTAL_URI')."/assets/email.png" }})

  Welcome **{{ $row->name }}**, we are delighted to have you on board in the Kanini ecosystem as our valued
  supplier. Your partnership is instrumental in our shared journey towards success.

  From the 1st of September 2024, all our Local Purchase Orders, will be accessed only through the Supplier Portal.

  To ensure a smooth onboarding process and to finalize your inclusion in our network, we kindly request you to click
  on the "Proceed" button below. This will direct you to the onboarding form that needs to be filled out.

  We appreciate your prompt attention to this matter and look forward to a fruitful partnership.

  <x-mail::button :url="$location" color="primary" align="left">
    Proceed
  </x-mail::button>
</x-mail::message>
