var pkg                     	= require('./package.json');
var buildBlockStyle   		= './block/build/style.css';
var buildEditorStyle   		= './block/build/editor.css';
var buildScript  		= './block/build/index.js';
var devBlockScript  		= './block/index.js';

var buildFiles      	    	= ['./**', '!dist/', '!/**/**/*.scss', '!' + devBlockScript, '!.gitattributes', '!node_modules/**', '!*.sublime-project', '!*.sublime-workspace', '!*.sublime-gulp.cache', '!package.json', '!package-lock.json', '!webpack.config.js', '!gulpfile.js', '!*.json', '!*.map', '!*.md', '!*.xml', '!*.log', '!*.DS_Store','!*.gitignore', '!TODO', '!*.git' ];
var buildDestination        	= './dist/'+ pkg.slug +'/';
var distributionFiles       	= './dist/'+ pkg.slug +'/**/*';

var bugReport               	= pkg.author_uri;
var destFile                	= pkg.slug+'.pot';
var lastTranslator          	= pkg.author;
var packageName             	= pkg.title;
var team                    	= pkg.author_shop;
var text_domain             	= '@@textdomain';
var translatableFiles       	= ['./**/*.php'];
var translatePath           	= './languages';

var cache        = require('gulp-cache');
var cleaner      = require('gulp-clean');
var copy         = require('gulp-copy');
var gulp         = require('gulp');
var minifycss    = require('gulp-uglifycss');
var notify       = require('gulp-notify');
var replace      = require('gulp-replace-task');
var runSequence  = require('run-sequence');
var sort         = require('gulp-sort');
var uglify       = require('gulp-uglify');
var wpPot        = require('gulp-wp-pot');
var zip          = require('gulp-zip');

/**
 * Tasks.
 */
gulp.task('clear', function () {
	cache.clearAll();
});

gulp.task( 'clean', function () {
	return gulp.src( ['./dist/*'] , { read: false } )
	.pipe(cleaner());
});

gulp.task( 'minify_block_styles', function () {
	gulp.src( buildBlockStyle, { base: './' } )
	.pipe( minifycss() )
	.pipe( gulp.dest( './' ) )
});

gulp.task( 'minify_editor_styles', function () {
	gulp.src( buildEditorStyle, { base: './' } )
	.pipe( minifycss() )
	.pipe( gulp.dest( './' ) )
});

gulp.task( 'minify_block_script', function() {
	gulp.src( buildScript, { base: './' } )
	.pipe( uglify() )
	.pipe( gulp.dest( './' ) )
});

gulp.task( 'generate_pot', function () {
	gulp.src( translatableFiles )
	.pipe( sort() )
	.pipe( wpPot( {
		domain        : text_domain,
		destFile      : destFile,
		package       : pkg.title,
		bugReport     : bugReport,
		lastTranslator: lastTranslator,
		team          : team
	} ))
	.pipe( gulp.dest( translatePath ) )
});

gulp.task( 'copy', function() {
    return gulp.src( buildFiles )
    .pipe( copy( buildDestination ) );
});

gulp.task( 'variables', function () {
	return gulp.src( distributionFiles )
	.pipe( replace( {
		patterns: [
		{
			match: 'pkg.version',
			replacement: pkg.version
		},
		{
			match: 'textdomain',
			replacement: pkg.textdomain
		},
		{
			match: 'pkg.title',
			replacement: pkg.title
		},
		{
			match: 'pkg.slug',
			replacement: pkg.slug
		},
		{
			match: 'pkg.license',
			replacement: pkg.license
		},
		{
			match: 'pkg.plugin_uri',
			replacement: pkg.plugin_uri
		},
		{
			match: 'pkg.author',
			replacement: pkg.author
		},
		{
			match: 'pkg.author_uri',
			replacement: pkg.author_uri
		},
		{
			match: 'pkg.requires',
			replacement: pkg.requires
		},
		{
			match: 'pkg.tested_up_to',
			replacement: pkg.tested_up_to
		},
		{
			match: 'pkg.tags',
			replacement: pkg.tags
		}
		]
	}))
	.pipe( gulp.dest( buildDestination ) );
});

gulp.task( 'zip', function() {
    return gulp.src( buildDestination + '/**', { base: 'dist' } )
    .pipe( zip( pkg.slug + '.zip' ) )
    .pipe( gulp.dest( './dist/' ) );
});

gulp.task( 'clean_after_zip', function () {
	return gulp.src( [ buildDestination, '!/dist/' + pkg.slug + '.zip'] , { read: false } )
	.pipe(cleaner());
});

gulp.task( 'build_complete', function () {
	return gulp.src( '' )
	.pipe( notify( { message: '👷 Your build of ' + pkg.title + ' is complete.', onLast: true } ) );
});

gulp.task( 'build', function( callback ) {
	runSequence( 'clear', 'clean', [ 'minify_block_styles', 'minify_editor_styles', 'minify_block_script', 'generate_pot' ], 'copy', 'variables', 'zip', 'build_complete', callback);
});
