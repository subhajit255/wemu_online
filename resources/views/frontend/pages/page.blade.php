 @extends('frontend.layout.app')
 @section('content')
     <div class="container my-5">
         <div class="row justify-content-center">
             <div class="col-lg-12 col-md-12">
                 <div class="card shadow-sm">
                     <div class="card-body">
                         {!! $data !!}
                     </div>
                 </div>
             </div>
         </div>
     </div>
 @endsection
