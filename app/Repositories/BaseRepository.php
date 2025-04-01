<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected $model;

    // Khởi tạo repository với model
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Lấy tất cả bản ghi dựa trên garage_id
    public function getAll($garage_id)
    {
        return $this->model->where('garage_id', $garage_id)->get();
    }

    // Tìm bản ghi theo ID
    public function find($id)
    {
        return $this->model->find($id);
    }

    // Tạo bản ghi mới
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    // Cập nhật bản ghi theo ID
    public function update($id, array $data)
    {
        $item = $this->model->find($id);
        if ($item) {
            $item->update($data);
            return $item;
        }
        return null;
    }

    // Xóa bản ghi theo ID
    public function delete($id)
    {
        $item = $this->model->find($id);
        if ($item) {
            $item->delete();
            return true;
        }
        return false;
    }
}
