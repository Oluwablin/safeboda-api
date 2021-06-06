<?php

namespace App\Http\Controllers\v1\Promo;

use App\Models\Promo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\DistanceTrait;
use Polyline;
use App\Http\Resources\PromoResource;
use App\Http\Requests\CreatePromoRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Http\Requests\ValidatePromoRequest;
use App\Http\Resources\PromoResourceCollection;

class PromoController extends Controller
{
    use DistanceTrait;

    /**
     * Display all promos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new PromoResourceCollection(Promo::withTrashed()->get());
    }

    /**
     * Display all active promos.
     *
     * @return \Illuminate\Http\Response
     */
    public function active()
    {
        return new PromoResourceCollection(Promo::withoutExpired()->get());
    }

    //Display all expired promos.
    public function expired()
    {
        return new PromoResourceCollection(Promo::onlyExpired()->get());
    }

    //Display all deactivated promos.
    public function deactivated()
    {
        return new PromoResourceCollection(Promo::onlyTrashed()->get());
    }

    //Deactivate a promo
    public function deactivate($id)
    {
        $promo = Promo::findByUuid($id);

        if ($promo == null) {
            abort(404, 'Promo Code not found');
        }


        if ($promo->delete()) {
            $response = [
                'status' => 'success',
                'message' => 'Promo Code Successfully deactivated'
            ];

            return response()->json($response, 200);
        }

        $response = [
            'status' => 'error',
            'message' => 'Error Deactivating Promo Code'
        ];

        return response()->json($response, 400);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePromoRequest $request)
    {
        if ($request->code == null) {
            $request->code = Promo::generate();
        }

        $venue_coordinates = $this->get_latitude_and_longitude($request->venue);

        if ($venue_coordinates == null) {
            $response = [
                'status' => 'error',
                'message' => 'Sorry, Location not found.'
            ];

            return response()->json($response, 400);
        }

        $expiry = Promo::fetch_ttl($request->expiry_date);

        $promo = Promo::create([
            'code' => $request->code,
            'venue' => $request->venue,
            'value' => $request->value,
            'radius' => $request->radius,
            'expires_at' => $expiry
        ]);

        if ($promo == null) {
            $response = [
                'status' => 'error',
                'message' => 'Error saving promo code.'
            ];

            return response()->json($response, 400);
        }


        $response = [
            'status' => 'success',
            'message' => 'New Promo Code Successfully saved.'
        ];

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Promo  $promo
     * @return \Illuminate\Http\Response
     */
    public function show(ValidatePromoRequest $request, $code)
    {
        $promo = Promo::where('code', $code)->first();

        if ($promo == null) {
            abort(404, 'Promo Code not found');
        }

        if ($promo->expired()) {
            $response = [
                'status' => 'error',
                'message' => 'Sorry. This promo code has already expired.'
            ];

            return response()->json($response, 400);
        }

        $origin_coordinates = $this->get_latitude_and_longitude($request->origin);

        $destination_coordinates = $this->get_latitude_and_longitude($request->destination);

        if ($origin_coordinates == null || $destination_coordinates == null) {
            $response = [
                'status' => 'error',
                'message' => 'Sorry. Locations not found.'
            ];

            return response()->json($response, 400);
        }

        $valid_code = $this->verify($promo->uuid, $origin_coordinates, $destination_coordinates);

        if ($valid_code == false) {
            $response = [
                'status' => 'error',
                'message' => 'Current location not valid for promo code.'
            ];

            return response()->json($response, 400);
        }


        $points = [$destination_coordinates, $origin_coordinates];

        $promo->polyline = Polyline::encode($points);

        return new PromoResource($promo);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Promo  $promo
     * @return \Illuminate\Http\Response
     */
    public function edit(Promo $promo)
    {
        //
    }

    /**
     * Update promo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Promo  $promo
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePromoRequest $request, $id)
    {
        $promo = Promo::findByUuid($id);

        if ($promo == null) {
            abort(404, 'Promo Code Not Found');
        }

        $venue_coordinates = $this->get_latitude_and_longitude($request->venue);

        if ($venue_coordinates == null) {
            $response = [
                'status' => 'error',
                'message' => 'Sorry. Location not found.'
            ];

            return response()->json($response, 400);
        }

        $update_promo = Promo::where('uuid', $id)->update([
            'code' => $request->code,
            'venue' => $request->venue,
            'value' => $request->value,
            'radius' => $request->radius,
            'expires_at' => $request->expiry_date
        ]);

        if ($update_promo == null) {
            $response = [
                'status' => 'error',
                'message' => 'Error updating promo code.'
            ];

            return response()->json($response, 400);
        }

        $response = [
            'status' => 'success',
            'message' => 'Promo Code successfully updated.'
        ];

        return response()->json($response, 200);
    }

    /**
     * Delete a promo.
     *
     * @param  \App\Models\Promo  $promo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $promo = Promo::findByUuid($id);

        if ($promo == null) {
            abort(404, 'Promo Code not found');
        }

        if ($promo->forceDelete()) {
            $response = [
                'status' => 'success',
                'message' => 'Promo Code Successfully deleted'
            ];

            return response()->json($response, 200);
        }

        $response = [
            'status' => 'error',
            'message' => 'Error Deleting Promo Code'
        ];

        return response()->json($response, 200);
    }
}
