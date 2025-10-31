<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserProduct;
use Illuminate\Auth\Access\Response;

class UserProductPolicy
{
     /**
     * Permite listar recursos. En tu caso el catálogo puede ser público,
     * pero como Laravel tipa User no-null aquí, devolvemos true para cualquier autenticado.
     * (Si necesitas acceso público, no uses esta policy en el listado público.)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Ver un recurso concreto (catálogo/detalle). Igual que arriba:
     * si el detalle es público, esta policy no se aplica; si se aplica, permite a autenticados.
     */
    public function view(User $user, UserProduct $userProduct): bool
    {
        return true;
    }

    /**
     * Crear piezas (cualquier usuario autenticado).
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Actualizar: solo el dueño o un admin.
     */
    public function update(User $user, UserProduct $userProduct): bool
    {
        return $user->user_id === $userProduct->user_id || $this->isAdmin($user);
    }

    /**
     * Eliminar: solo el dueño o un admin.
     */
    public function delete(User $user, UserProduct $userProduct): bool
    {
        return $user->user_id === $userProduct->user_id || $this->isAdmin($user);
    }

    /**
     * Restaurar (si llegaras a usar soft deletes): solo admin.
     */
    public function restore(User $user, UserProduct $userProduct): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Eliminación permanente: solo admin.
     */
    public function forceDelete(User $user, UserProduct $userProduct): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Helper para detectar admin. AJUSTA a tu implementación real.
     * - Si usas roles con una relación/atributo, modifica esta lógica.
     * - Ejemplos contemplados: $user->role === 'admin' o $user->is_admin === true.
     */
    private function isAdmin(User $user): bool
    {
        // Ejemplos comunes; cambia por tu lógica real de roles/permisos:
        if (property_exists($user, 'role') && $user->role === 'admin') {
            return true;
        }
        if (property_exists($user, 'is_admin') && (bool) $user->is_admin === true) {
            return true;
        }
        // Si usas Spatie Permissions:
        // return method_exists($user, 'hasRole') && $user->hasRole('admin');

        return false;
    }
}
