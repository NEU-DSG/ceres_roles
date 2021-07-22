<?php 


class Ceres_Roles {
  
  public $wpRoles = ['administrator' => 'Administrator',
                     'editor'        => 'Editor',
                     'author'        => 'Author',
                     'contributor'   => 'Contributor',
                     'subscriber'    => 'Subscriber',
  ];
  
  public $ceresRoles = ['ceres_student'    => 'Student',
                        'ceres_ta'         => 'Teaching Assistant',
                        'ceres_teacher'    => 'Teacher',
                        'ceres_site_owner' => 'Site Owner',
  ];
  
  
  
  public function addCeresRole($roleName, $display_name) {
    if (! $this->roleExists($roleName)) {
      $this->setupRole($roleName); // @TODO I'm liking the idea of this method less and less
      
      //make WP do its thing
      add_role( $roleName, $display_name, $capabilities = array() );
      
    } else {
      throw Exception("Role $roleName already exists");
    }
    

  }
  
  
  public function addAllCeresRoles() {
    foreach ($this->ceresRoles as $ceresRole => $displayName) {
      $this->addCeresRole($ceresRole);
    }
  }

  
  public function removeCapabilityFromRole($role, $capability) {
    if (is_string($role)) {
      $roleObject = get_role($role);
      if (! $roleObject) {
        throw Exception("string $role does not exist");
      }
    } else {
      $roleObject = $role;
    }
  }
  
  public function addCapabilityToRole($role, $capability) {
    if (is_string($role)) {
      $roleObject = get_role($role);
      if (! $roleObject) {
        throw Exception("string $role does not exist");
      }
    } else {
      $roleObject = $role;
    }
    
  }
  
  /**
   * Mostly a wrapper around WP's functionality
   * 
   * @param string|Object $role
   * @param string $user
   */
  
  public function hasRole($role, $userLogin = null) {
    if (is_string($role)) {
      $roleObject = get_role($role);
      if (! $roleObject) {
        throw Exception("string $role does not exist");
      }
    } else {
      $roleObject = $role;
    }
    //if user isn't supplied, use the current user
    if ( is_null($userLogin) ) {
      $userObject = wp_get_current_user();
      // @TODO: look up correct $user props
    }
    
    // make WP do it's thing
    return wp_get_user_by('login', $userLogin); 
  }
  
  /**
   * Mostly a wrapper around WP's functionality
   * 
   * @param string $capability
   * @param string|WP user object $user
   */
  
  // @TODO: too much duplication/wrapping around native wp functions?
  // depends on where I want to call it, and how. a private method?
  public function hasCapability($capability, $userLogin = null) {
    //if user isn't supplied, use the current user
    if (! $user) {
      $userObject = wp_get_current_user();
    } else {
      $userObject = wp_get_user_by('login', $userLogin);
    }
    
    return $userObject->has_cap($capability);
  }
  
  
  private function roleExists($roleName) {
    $roleObject = get_role( $roleName );
    if (! $roleObject ) {
      return false;
    }
    return true;
  }
  
  private function copyCapabilities($parentRoleName, $childRoleName) {
    // get the parent's capabilities as array
    $parentRoleObject = get_role($parentRoleName);
    if (is_null($parentRoleObject)) {
      throw new Exception("No role named $parentRoleName");
    }
    
    $childRoleObject = get_role($childRoleName);
    if (is_null($childRoleObject)) {
      throw new Exception("No role named $childRoleName");
    }
    
    foreach ($parentRoleObject->capabilities as $capability) {
      $childRoleObject->add_cap($capability);
    }
  }
}

