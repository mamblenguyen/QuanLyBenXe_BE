<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\Bus\StoreBusRequest;
use App\Http\Requests\System\Bus\UpdateBusRequest;
use App\Repositories\Implementations\BusModelRepositoryInterface;
use App\Http\Resources\System\BusModel\BusModelListResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\BusImage;
class BusModelController extends Controller
{
    protected $busModelRepository;

    public function __construct(BusModelRepositoryInterface $busModelRepository)
    {
        $this->busModelRepository = $busModelRepository;
    }

    public function index()
    {
        $buses = $this->busModelRepository->getAllOutGarage();

        if ($buses->isNotEmpty()) {
            return ResponseService::customResponse(BusModelListResource::collection($buses), 'Get all Bus Models successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Bus Models not found', 404);
    }

    public function show($id, Request $request)
    {
        $garage_id = $request->input('garage_id');
        if (!$garage_id) {
            return ResponseService::customResponse(null, 'Garage ID is required', 400);
        }
    
        // Tìm bus theo id và kiểm tra garage_id
        $bus = $this->busModelRepository->find($id);
        if (!$bus || $bus->garage_id !== $garage_id) {
            return ResponseService::customResponse(null, 'Bus not found or access denied !!', 403);
        }
    
        // Nạp dữ liệu liên quan (bao gồm busModel và busImages)
        $bus->load('busModel', 'images'); // 'images' là phương thức quan hệ trong model Bus
    
        return ResponseService::customResponse(new BusModelListResource($bus), 'Show bus successfully!!', 200);
    }
    

    public function store(StoreBusRequest $request)
    {
        // Lấy garage_id từ user đã đăng nhập
        $garage_id = auth()->user()->garage_id;
    
        // Lấy dữ liệu từ request
        $requestData = $request->all();
        $requestData['garage_id'] = $garage_id;
    
        // Tạo xe mới
        $bus = $this->busModelRepository->create($requestData)->load('busModel');
    
        // Kiểm tra xem có ảnh không và lưu ảnh vào cơ sở dữ liệu
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Lấy tên gốc của ảnh
                $fileName = time() . '_' . $image->getClientOriginalName();
    
                // Lưu ảnh vào storage (thư mục buses)
                $path = Storage::disk('s3')->putFileAs('bus', $image, $fileName);
    
                if ($path) {
                    $imageUrl = Storage::disk('s3')->url($path);
    
                    // Lưu thông tin ảnh vào bảng bus_images
                    BusImage::create([
                        'bus_id' => $bus->id,
                        'image_path' => $imageUrl,
                    ]);
                }
            }
        }
    
        // Trả về response thành công với dữ liệu bus đã tạo
        return ResponseService::customResponse(new BusModelListResource($bus), 'Create bus successfully !!', 201);
    }
    
    

    public function update(UpdateBusRequest $request, $id)
    {

        $userGarageId = auth()->user()->garage_id;
        $bus = $this->busModelRepository->find($id);
        if (!$bus) {
            return ResponseService::customResponse(null, 'Bus not found !!', 404);
        }
        if ($bus->garage_id !== $userGarageId) {
            return ResponseService::customResponse(null, 'Access denied! You can only update buses from your own garage.', 403);
        }
        $validatedData = $request->validated();
        $bus = $this->busModelRepository->update($id, $validatedData)->load('busModel');

        return ResponseService::customResponse(new BusModelListResource($bus), 'Update bus successfully !!', 200);
    }




    public function destroy($id, Request $request)
    {
        $garage_id = $request->input('garage_id');
        $bus = $this->busModelRepository->find($id);
        if (!$bus || $bus->garage_id !== $garage_id) {
            return ResponseService::customResponse(null, 'Bus not found or access denied !!', 403);
        }
        $deleted = $this->busModelRepository->delete($id);
        if ($deleted) {
            return ResponseService::customResponse(null, 'Delete bus successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Bus not found !!', 404);
    }


  

}
