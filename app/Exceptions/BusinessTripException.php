<?php

namespace App\Exceptions;

use Exception;

class BusinessTripException extends Exception
{
    /**
     * Buat exception untuk status transition yang tidak valid.
     * 
     * Gunakan ini ketika:
     * - User coba submit pengajuan yang bukan DRAFT
     * - User coba approve/reject pengajuan yang bukan SUBMITTED
     * - User coba cancel pengajuan yang sudah APPROVED/REJECTED
     * 
     * HTTP Status Code: 422 (Unprocessable Entity)
     * 
     * Contoh penggunaan:
     * throw BusinessTripException::invalidStatus('Hanya draft yang bisa disubmit.');
     * 
     * @param string $message Pesan error yang akan ditampilkan ke user
     * @return self Instance exception dengan HTTP code 422
     */
    public static function invalidStatus(string $message): self
    {
        return new self($message, 422);
    }

    /**
     * Buat exception untuk aksi yang tidak diizinkan (unauthorized).
     * 
     * Gunakan ini ketika:
     * - User coba approve/reject tapi bukan role SDM
     * - User coba akses pengajuan orang lain tanpa permission
     * - User coba edit pengajuan yang bukan miliknya
     * 
     * HTTP Status Code: 403 (Forbidden)
     * 
     * Contoh penggunaan:
     * throw BusinessTripException::unauthorized('Hanya SDM yang bisa approve.');
     * throw BusinessTripException::unauthorized(); // Pakai default message
     * 
     * @param string $message Pesan error (default: 'Anda tidak memiliki akses untuk melakukan aksi ini.')
     * @return self Instance exception dengan HTTP code 403
     */
    public static function unauthorized(string $message = 'Anda tidak memiliki akses untuk melakukan aksi ini.'): self
    {
        return new self($message, 403);
    }

    /**
     * Buat exception untuk resource yang tidak ditemukan.
     * 
     * Gunakan ini ketika:
     * - Pengajuan dengan ID tertentu tidak ada di database
     * - Kota tidak ditemukan saat create/update pengajuan
     * - User tidak ditemukan
     * 
     * HTTP Status Code: 404 (Not Found)
     * 
     * Contoh penggunaan:
     * throw BusinessTripException::notFound('Pengajuan tidak ditemukan.');
     * throw BusinessTripException::notFound(); // Pakai default message
     * 
     * @param string $message Pesan error (default: 'Data tidak ditemukan.')
     * @return self Instance exception dengan HTTP code 404
     */
    public static function notFound(string $message = 'Data tidak ditemukan.'): self
    {
        return new self($message, 404);
    }
}
