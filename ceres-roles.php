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
  
  
  
  public function addCeresRole($role, $display_name) {
    if (! $this->roleExists($role)) {
      $this->setupRole($role);
      
      //make WP do its thing
      add_role( $role, $display_name, $capabilities = array() );
      
    } else {
      throw Exception("Role already exists");
    }
  }
  
  
  public function addCeresRoles() {
    foreach ($this->ceresRoles as $ceresRole => $displayName) {
      $this->addCeresRole($ceresRole);
    }
        
    
  }

  
  public function removeCapabilityToRole($role, $capability) {
    
  }
  
  public function addCapabilityToRole($role, $capability) {
    
    
  }
  
  /**
   * Mostly a wrapper around WP's functionality
   * 
   * @param string $role
   * @param string $user
   */
  
  public function hasRole($role, $userSlug = null) {
    //if user isn't supplied, use the current user
    if (! $user) {
      $user = wp_get_current_user();
    }
    
    // make WP do it's thing
  }
  
  /**
   * Mostly a wrapper around WP's functionality
   * 
   * @param string $capability
   * @param string|WP user object $user
   */
  
  public function hasCapability($capability, $userSlug = null) {
    //if user isn't supplied, use the current user
    if (! $user) {
      $userObject = wp_get_current_user();
    } else {
      $userObject = wp_get_user_by('slug', $userSlug);
    }
    
    return $userObject->has_cap($capability);
  }
  
  
  private function roleExists($role) {
    $roleObject = get_role( $role );
    if (! $roleObject ) {
      return false;
    }
    return true;
  }
  
  private function setupRole($role) {
    
  }
  
}