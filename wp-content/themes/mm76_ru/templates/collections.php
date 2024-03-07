<section class="section-collections mt-100">
    <div class="cont">
        <div class="section-header">
            <div class="section-header-desc collections">
                <span>Всё по полочкам</span>
            </div>

            <div class="site-title title-colls">Наши коллекции
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100" fill="none">
                    <path d="M73.7248 66.5838C60.388 64.8026 46.6711 68.7546 36.5493 77.5622C37.3585 77.8712 38.1636 78.1092 38.9729 78.4182C38.6672 62.3037 30.424 48.1576 22.3661 34.7143C21.5356 35.2631 20.7052 35.8119 19.8789 36.4319C26.6311 41.1722 33.5815 45.6152 40.7258 49.6899C41.6194 50.2081 43.6765 49.7296 43.3774 48.3196C40.6515 37.4878 40.9985 26.3315 44.2498 15.6459C43.3266 15.8433 42.336 16.116 41.4129 16.3133C47.1024 24.9712 51.8265 34.3286 55.4251 44.1094C55.9307 45.3644 57.7813 45.0408 58.3715 44.078C63.2116 35.9396 69.94 29.3314 78.2868 24.5548C77.4649 24.0324 76.7146 23.5057 75.8927 22.9832C70.7611 35.9215 68.1839 49.4939 69.5816 63.4024C69.7711 65.3899 73.1939 64.902 73.0803 62.9813C72.2263 49.7546 73.8087 36.3837 78.7589 24.0272C79.3872 22.4911 77.5495 21.8145 76.3649 22.4556C67.6682 27.3956 60.4635 34.4603 55.4336 43.0382C56.4411 43.05 57.3727 42.9949 58.3801 43.0067C54.634 33.1632 49.7623 23.7431 43.9886 14.8761C43.1963 13.6381 41.4132 13.8863 40.9198 15.2717C37.4662 26.1834 37.136 37.6243 39.9504 48.7364C40.8567 48.2545 41.6956 47.848 42.6019 47.3661C35.4576 43.2914 28.5072 38.8484 21.755 34.108C20.7055 33.3849 18.3954 34.4495 19.2678 35.8255C27.313 49.0554 35.5394 62.917 35.9042 78.8138C35.8956 79.8851 37.569 80.2144 38.3278 79.6699C48.1082 71.168 60.5184 67.0078 73.3916 68.2453C74.5424 68.2486 74.8882 66.8005 73.7248 66.5838Z" fill="#FF81C9"/>
                    <path d="M42.798 86.8171C45.2795 86.2513 47.0853 83.979 49.235 82.6643C52.3585 80.8031 55.8688 79.408 59.4877 78.6351C62.6811 77.9573 65.9708 77.6929 69.2178 77.9201C72.6079 78.1387 75.8573 79.6231 79.1717 79.7764C80.3931 79.774 81.3297 78.601 80.198 77.6899C77.7832 75.7371 73.3107 75.4427 70.2152 75.3463C66.3955 75.153 62.6802 75.5125 58.9979 76.4287C55.5263 77.2627 52.2268 78.5755 49.1708 80.3629C46.9453 81.6122 42.6926 83.8196 42.2762 86.4987C42.2845 86.6379 42.5832 86.8298 42.798 86.8171Z" fill="#FF81C9"/>
                </svg>
            </div>

        </div>

        <div class="collections-grid tablet-hidden mob_vis">
        <?php
                    
                    $taxes = get_field('vyberite_kollekcziyu');
                    $total = count($taxes);
                    //print_r ($total);
                    if( $taxes ) {  
                        $i = 1;
                        foreach($taxes as $tax) {      
                        $i++;         
                                   
                            $term = get_term($tax, 'product_cat');
                          
                            $image_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
                            
                            ?>
                            <a href="<?= get_term_link($tax, 'product_cat'); ?>" class="card-collection">
                                <picture>
                                    <?= wp_get_attachment_image( $image_id, 'full' ); ?>
                                </picture>
                                <div class="btn-abs">
                                    <?= $term->name; ?>
                                </div>
                            </a>  

                            <?php
                            if ($i == 3){
                                echo '<div class="row-collections">';
                            }   
                            if ($i == $total){
                                echo '</div>';
                            }
                            
                        }
                    
                        
                    }
                  
                   
                ?>

        </div>
    
    <div class="collections-grid tablet-vis">
        
            <?php
           
           if( $taxes ) {  
            $i = 1;
            foreach($taxes as $tax) {      
            $i++;         
                       
                $term = get_term($tax, 'product_cat');
              
                $image_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
                
                ?>
                <a href="<?= get_term_link($tax, 'product_cat'); ?>" class="card-collection">
                    <picture>
                        <?= wp_get_attachment_image( $image_id, 'full' ); ?>
                    </picture>
                    <div class="btn-abs">
                        <?= $term->name; ?>
                    </div>
                </a>  
                
                <?php
                if ($i == 2){
                    echo '<div class="row-collections">';
                }   
                if ($i == $total){
                    echo '</div>';
                }
                
            }
        
            
        }
      
          ?>
        </div>

        <?php 
                ?>
                <div id="loadmore" class="text-align-center mt-30">
                    <button class="btn">Смотреть ещё</button>
                </div> 
                <?php
        ?>
        </div>
</section>
