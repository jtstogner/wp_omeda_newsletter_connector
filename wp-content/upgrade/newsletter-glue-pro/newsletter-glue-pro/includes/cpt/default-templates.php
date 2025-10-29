<?php
/**
 * Default Templates.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Default_Templates class.
 */
class NGL_Default_Templates {

	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Pattern exists?
	 */
	public function post_by_meta_exists( $meta_key, $value ) {
		$posts = get_posts( array(
			'post_type'		=> 'ngl_template',
			'post_status'	=> get_post_stati(),
			'meta_key'  	=> $meta_key,  // phpcs:ignore
			'meta_value' 	=> $value, // phpcs:ignore
			'number'		=> 1,
		) );

		return ! empty( $posts ) ? $posts[0]->ID : false;
	}

	/**
	 * Create.
	 */
	public function create( $include = false ) {

		global $current_user;

		$this->create_embeds();

		$defaults = $this->get_templates();

		$found_post = 0;

		foreach( $defaults as $key => $template ) {

			if ( $include && isset( $defaults[ $include ] ) && $key != $include ) {
				continue;
			}

			$args = array(
				'post_type' 	=> 'ngl_template',
				'post_status'	=> 'publish',
				'post_author'	=> $current_user->ID,
				'post_title'	=> $template[ 'title' ],
				'post_content'	=> $template[ 'content' ],
			);

			$found_post = $this->post_by_meta_exists( '_ngl_core_template', $key );

			if ( $found_post ) {
				wp_update_post( array_merge( array( 'ID' => $found_post ), $args ) );
				$this->update_theme( $key, $found_post, $template[ 'theme' ] );
				continue;
			}

			$post_id = wp_insert_post( $args );

			update_post_meta( $post_id, '_ngl_core_template', $key );

			$this->update_theme( $key, $post_id, $template[ 'theme' ] );
		}
	}

	/**
	 * Update template theme.
	 */
	public function update_theme( $key, $post_id, $theme = array() ) {

		delete_post_meta( $post_id, '_newsletterglue_theme' );

		if ( ! empty( $theme ) ) {
			update_post_meta( $post_id, '_newsletterglue_theme', $theme );
		}

		if ( in_array( $key, array( 'template_11', 'template_12', 'template_13' ) ) ) {
			update_post_meta( $post_id, '_is_automation', 'yes' );
			wp_set_object_terms( $post_id, array( 'automations' ), 'ngl_template_category' );
		}
	}

	/**
	 * Get templates list.
	 */
	public function get_templates() {

		for ( $i = 1; $i<=13; $i++ ) {

			$content = apply_filters( "ng_default_template_{$i}_content", "Template {$i}" );
			$content = addslashes( $content );
			$content = str_replace( 'http://localhost/wp-content/plugins/newsletter-glue-pro/assets', NGL_PLUGIN_URL . 'assets', $content );
            $content = str_replace( 'http://localhost/wp-content/plugins/newsletter-glue-pro-v2/assets', NGL_PLUGIN_URL . 'assets', $content );
			$content = str_replace( 'http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96\u0026d=mm\u0026r=g', '', $content );
			$content = str_replace( 'http://0.gravatar.com/avatar/045ef9284c033798e402b62c6e28a4b7?s=96&amp;d=mm&amp;r=g', '', $content );

			$content = str_replace( '{{ admin_address,fallback=21 Park Road }}', '{{ admin_address,fallback=' . get_option( 'newsletterglue_admin_address' ) . ' }}', $content );

			$content = str_replace( '{{ admin_name,fallback=Newsletter Glue }}', '{{ admin_name,fallback=' . get_option( 'newsletterglue_admin_name', get_bloginfo( 'name' ) ) . ' }}', $content );

			$templates[ "template_{$i}" ] = array(
				'title' 	=> apply_filters( "ng_default_template_{$i}_title", "Template {$i}" ),
				'content' 	=> $content,
				'theme'		=> apply_filters( "ng_default_template_{$i}_theme", array() ),
			);
		}

		return $templates;

	}

	/**
	 * Create embeds.
	 */
	public function create_embeds() {

		$embed_1 = array(
			1 => array(
			'post_id' => 'https://www.himama.com/blog/the-importance-of-cognitive-activities-in-early-education',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://www.himama.com/blog/the-importance-of-cognitive-activities-in-early-education',
			'is_remote' => 1,
			'ID' => 'https://www.himama.com/blog/the-importance-of-cognitive-activities-in-early-education',
			'title' => 'The importance of cognitive activities in early education',
			'post_content' => 'Young children need a variety of cognitive activities available in the classroom for their learning, growth and development.',
			'image_url' => 'https://www.himama.com/blog/wp-content/uploads/2022/03/General-Blog-Post-Template-62.png',
			),
		);

		$embed_2 = array(
			3 => array(
			'post_id' => 'https://www.himama.com/blog/diversity-in-an-early-years-classroom',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://www.himama.com/blog/diversity-in-an-early-years-classroom',
			'is_remote' => 1,
			'ID' => 'https://www.himama.com/blog/diversity-in-an-early-years-classroom',
			'title' => 'Diversity in an early years classroom',
			'post_content' => 'Diversity is important to teach children in their classroom. Use these tips to ensure that preschoolers learn about different cultures!',
			'image_url' => 'https://www.himama.com/blog/wp-content/uploads/2022/03/General-Blog-Post-Template-59.png',
			),

			2 => array(
			'post_id' => 'https://www.himama.com/blog/try-these-in-your-classroom-activities-spotlight',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://www.himama.com/blog/try-these-in-your-classroom-activities-spotlight',
			'is_remote' => 1,
			'ID' => 'https://www.himama.com/blog/try-these-in-your-classroom-activities-spotlight',
			'title' => 'Try these in your classroom: activities spotlight',
			'post_content' => 'Try these activities in your early childhood education classroom. They are great for the growth and development of young children!',
			'image_url' => 'https://www.himama.com/blog/wp-content/uploads/2022/03/General-Blog-Post-Template-58.png',
			),

			1 => array(
			'post_id' => 'https://www.himama.com/blog/the-importance-of-emotional-intelligence-in-young-learners',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://www.himama.com/blog/the-importance-of-emotional-intelligence-in-young-learners',
			'is_remote' => 1,
			'ID' => 'https://www.himama.com/blog/the-importance-of-emotional-intelligence-in-young-learners',
			'title' => 'The importance of emotional intelligence in young learners',
			'post_content' => 'Social-emotional learning is the most important subject we can teach our children. Read more on how to set this up in your classroom!',
			'image_url' => 'https://www.himama.com/blog/wp-content/uploads/2022/03/General-Blog-Post-Template-60.png',
			)
		);

		$embed_3 = array(
			3 => array(
			'post_id' => 'https://lifegoalsmag.com/how-add-play-workday',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://lifegoalsmag.com/how-add-play-workday',
			'is_remote' => 1,
			'ID' => 'https://lifegoalsmag.com/how-add-play-workday',
			'title' => 'How To Start Adding More Play To Your Workday | Life Goals Mag',
			'post_content' => 'Have you ever thought about incorporating play into your workday? Are you having enough fun at work? For some, it might feel like you’re working a job that might not feel playful and fun, but the truth is, there are many ways we can add play into our daily work routines.  Listen to the episode!   ▶︎ Listen on Apple Podcasts | Spotify | Google Podcasts | Stitcher Tried and true methods for adding more play into work A quick […]',
			'image_url' => 'https://i0.wp.com/lifegoalsmag.com/wp-content/uploads/2022/01/play-at-work-making-it-part-of-your-lifestyle.jpeg',
			),

			2 => array(
			'post_id' => 'https://lifegoalsmag.com/career-pancake-tree',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://lifegoalsmag.com/career-pancake-tree',
			'is_remote' => 1,
			'ID' => 'https://lifegoalsmag.com/career-pancake-tree',
			'title' => 'When it Comes to Your Career, Are You a Pancake or a Tree? | Life Goals Mag',
			'post_content' => 'In one of my previous roles, I used to organize monthly guest lectures for my university students from various active professionals in their target industries. I was working with creative media students, so we focused on showcasing the breadth of different opportunities and potential career paths that exist in industries like animation, music, film, design, and games development. One of the professionals I bought in to do a talk was primarily a graphic designer, but she handed out a piece […]',
			'image_url' => 'https://i0.wp.com/lifegoalsmag.com/wp-content/uploads/2022/01/pancakes-personality-test.jpeg',
			),

			1 => array(
			'post_id' => 'https://lifegoalsmag.com/sleep-hygiene-productivity-hack',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://lifegoalsmag.com/sleep-hygiene-productivity-hack',
			'is_remote' => 1,
			'ID' => 'https://lifegoalsmag.com/sleep-hygiene-productivity-hack',
			'title' => '10 Sleep Hygiene Habits To Improve Your Slumber (And Productivity) | Life Goals Mag',
			'post_content' => 'Since launching my business, Cacti Wellness Collective, about a year ago, let’s just say there have been some looooong nights. While I’m not glorifying the idea of #teamnosleep, I understand the feeling of always having more work to do and/or not having enough minutes in the day. That being said, after going HARD on late nights & all nighters for the first six months of business, I reached a point of extreme exhaustion and found myself feeling overly emotional, physically […]',
			'image_url' => 'https://i0.wp.com/lifegoalsmag.com/wp-content/uploads/2021/05/evening-routine-bath-ritual-scaled-e1620234262645.jpeg',
			)

		);

		$embed_4 = array(
			3 => array(
			'post_id' => 'https://quillette.com/2020/09/30/pasha-glubb-and-avoiding-the-fate-of-empires',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://quillette.com/2020/09/30/pasha-glubb-and-avoiding-the-fate-of-empires',
			'is_remote' => 1,
			'ID' => 'https://quillette.com/2020/09/30/pasha-glubb-and-avoiding-the-fate-of-empires',
			'title' => 'John Glubb and Avoiding the Fate of Empires',
			'post_content' => 'Empires rise, and empires fall. This fact of history—so obvious looking backwards—is all but inconceivable to those living through an empire’s peak. Human life is so short in the scheme of civilisations that we tend to overemphasise the importance and length of our own era, while past',
			'image_url' => 'https://d24fkeqntp1r7r.cloudfront.net/wp-content/uploads/2020/09/23182129/Majoor_Kheiralla_Jarrah_en_Generaal_Glubb_Pasha_Bestanddeelnr_255-5096.jpg',
			),

			2 => array(
			'post_id' => 'https://www.ozy.com/the-new-and-the-next/sci-fi-doesnt-have-to-be-depressing-welcome-to-solarpunk/82586/?utm_source=lesley.pizza&utm_medium=email',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://www.ozy.com/the-new-and-the-next/sci-fi-doesnt-have-to-be-depressing-welcome-to-solarpunk/82586/?utm_source=lesley.pizza&utm_medium=email',
			'is_remote' => 1,
			'ID' => 'https://www.ozy.com/the-new-and-the-next/sci-fi-doesnt-have-to-be-depressing-welcome-to-solarpunk/82586/?utm_source=lesley.pizza&utm_medium=email',
			'title' => 'Sci-Fi Doesn&rsquo;t Have to Be Depressing: Welcome to Solarpunk',
			'post_content' => 'Black Mirror got you down? Check out this new, optimistic sci-fi genre.',
			'image_url' => 'https://assets.ozy.com/ozy-prod/2019/07/gettyimages697451536.jpg?width=1200&height=630',
			),

			1 => array(
			'post_id' => 'https://www.wnyc.org/story/420-the-lost-cities-of-geo',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://99percentinvisible.org/episode/the-lost-cities-of-geo',
			'is_remote' => 1,
			'ID' => 'https://www.wnyc.org/story/420-the-lost-cities-of-geo',
			'title' => 'The Lost Cities of Geo - 99% Invisible',
			'post_content' => 'The first time that David Bohnett heard about the internet, he knew that this was going to be a technology that was about to change the world. Today, David is a philanthropist and tech entrepreneur, but back in the early 1990s he really wanted to get on the ground floor of this brand new medium.',
			'image_url' => 'https://ychef.files.bbci.co.uk/976x549/p086w8pb.jpg',
			)
		);

		$embed_5 = array(
			4 => array(
			'post_id' => 'https://www.timeout.com/singapore/bars-and-pubs/republic',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://www.timeout.com/singapore/bars-and-pubs/republic',
			'is_remote' => 1,
			'ID' => 'https://www.timeout.com/singapore/bars-and-pubs/republic',
			'title' => 'Republic is a swanky new bar at the refurbished East Wing of Ritz-Carlton Singapore',
			'post_content' => 'This well-appointed drinking den comes inspired by the 1960s.',
			'image_url' => 'https://media.timeout.com/images/105768948/image.jpg',
			),

			3 => array(
			'post_id' => 'https://www.timeout.com/singapore/bars-and-pubs/taylor-adam',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://www.timeout.com/singapore/bars-and-pubs/taylor-adam',
			'is_remote' => 1,
			'ID' => 'https://www.timeout.com/singapore/bars-and-pubs/taylor-adam',
			'title' => 'First look: Behind the doors of this tailor lies an intimate speakeasy',
			'post_content' => 'Taylor Adam provides a charming escape into a world of inspired cocktails.',
			'image_url' => 'https://media.timeout.com/images/105822668/image.jpg',
			),

			2 => array(
			'post_id' => 'https://www.timeout.com/singapore/bars-and-pubs/analogue',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://www.timeout.com/singapore/bars-and-pubs/analogue',
			'is_remote' => 1,
			'ID' => 'https://www.timeout.com/singapore/bars-and-pubs/analogue',
			'title' => 'Bar Review: Plant-based cocktails and meatless plates shine at Analogue',
			'post_content' => 'Analogue is a new plant-based concept by award-winning Native that champions sustainability through its food and drinks.',
			'image_url' => 'https://media.timeout.com/images/105831982/image.jpg',
			),

			1 => array(
			'post_id' => 'https://www.timeout.com/singapore/bars-and-pubs/the-elephant-room',
			'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=https://www.timeout.com/singapore/bars-and-pubs/the-elephant-room',
			'is_remote' => 1,
			'ID' => 'https://www.timeout.com/singapore/bars-and-pubs/the-elephant-room',
			'title' => 'The Elephant Room: a spicy drinking hole that uncovers hidden stories of Singapore',
			'post_content' => 'Helmed by Yugnes Susela, the former head bartender of Smoke & Mirrors, The Elephant Room is a spice-forward, unapologetically Indian bar that&rsquo;s shaking up Singapore&rsquo;s scene',
			'image_url' => 'https://media.timeout.com/images/105551288/image.jpg',
			)

		);

		update_option( 'ngl_articles_7eb777d7ce6f480f98dee460e2106feb', $embed_1 );
		update_option( 'ngl_articles_90a3794d32b74470a227bed106318696', $embed_2 );
		update_option( 'ngl_articles_e712b015b18e495eaa81b587571e4b7c', $embed_3 );
		update_option( 'ngl_articles_f633367d464c4f92b1f9d6da0767c10a', $embed_4 );
		update_option( 'ngl_articles_9c3b961ceb6e4ccabc77baf58cc79cc7', $embed_5 );

	}

}
